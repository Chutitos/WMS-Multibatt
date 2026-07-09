<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\ProductLocationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->filled('cliente')) {
            $query->where('cliente_nombre', 'like', '%' . $request->string('cliente') . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->date('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->date('fecha_hasta'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('active', true)->orderBy('name')->get();

        return view('orders.create', compact('products'));
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Las órdenes nacen liberadas: quien las crea (admin/jefe) ya
            // decidió que van a bodega, así que aparecen de inmediato en
            // "Por preparar" sin el paso manual de liberación.
            $order = Order::create([
                'source_type' => 'manual',
                'source_reference' => null,
                'cliente_nombre' => $validated['cliente_nombre'],
                'rut_cliente' => $validated['rut_cliente'] ?? null,
                'tipo_entrega' => $validated['tipo_entrega'],
                'estado' => OrderStatus::LIBERADO,
                'observaciones' => $validated['observaciones'] ?? null,
                'creado_por' => $request->user()->id,
                'liberado_por' => $request->user()->id,
                'fecha_liberacion' => now(),
            ]);

            foreach ($validated['productos'] as $producto) {
                $product = Product::findOrFail($producto['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'producto_codigo' => $product->sku,
                    'producto_nombre' => $product->name,
                    'cantidad_solicitada' => $producto['cantidad'],
                    'cantidad_confirmada' => 0,
                ]);
            }

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'creado',
                'descripcion' => 'Orden creada.',
                'user_id' => $request->user()->id,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'liberado',
                'descripcion' => 'Orden liberada automáticamente a bodega al crearse.',
                'user_id' => $request->user()->id,
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Orden creada. Ya está en bodega, en "Por preparar".');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error al crear la orden.');
        }
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items', 'creator', 'releaser', 'events.user');

        return view('orders.show', compact('order'));
    }

    /**
     * Documento imprimible de la orden: papeleta de preparación mientras
     * está en curso, comprobante de entrega cuando ya se entregó.
     */
    public function imprimir(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items.product', 'creator');

        return view('orders.imprimir', compact('order'));
    }

    public function liberar(Request $request, Order $order)
    {
        if (! $order->estado->canTransitionTo(OrderStatus::LIBERADO)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden liberar órdenes en estado creado.');
        }

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'estado' => OrderStatus::LIBERADO,
                'liberado_por' => $request->user()->id,
                'fecha_liberacion' => now(),
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'liberado',
                'descripcion' => 'Orden liberada a bodega.',
                'user_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Orden liberada correctamente. Ya está disponible en Bodega.');
    }

    public function cancelar(Request $request, Order $order)
    {
        if (! $order->canBeCancelledBy($request->user())) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'No puedes cancelar esta orden en su estado actual.');
        }

        $devueltas = 0;

        DB::transaction(function () use ($request, $order, &$devueltas) {
            // Las unidades ya escaneadas vuelven a los pallets de donde
            // salieron: cancelar no puede hacer desaparecer stock físico.
            $devueltas = $this->devolverStockEscaneado($order, $request->user()->id);

            $order->update([
                'estado' => OrderStatus::CANCELADO,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'cancelado',
                'descripcion' => 'Orden cancelada.'
                    . ($devueltas > 0 ? " Se devolvieron {$devueltas} unidad(es) a sus estantes." : ''),
                'user_id' => $request->user()->id,
            ]);
        });

        $mensaje = 'Orden cancelada correctamente.'
            . ($devueltas > 0 ? " Las {$devueltas} unidad(es) escaneadas volvieron a sus estantes: devuélvelas físicamente a su lugar." : '');

        return redirect()->route('orders.index')->with('success', $mensaje);
    }

    /**
     * Reintegra a los pallets de origen las unidades ya escaneadas de una
     * orden (líneas de picking). Si el pallet original ya no existe, se
     * crea una existencia nueva en el mismo estante, sin puesto, para que
     * el jefe la reubique. Todo queda en el historial de existencias.
     */
    private function devolverStockEscaneado(Order $order, int $userId): int
    {
        $total = 0;

        $order->load('items.picks.productLocation');

        foreach ($order->items as $item) {
            foreach ($item->picks as $pick) {
                if ($pick->cantidad <= 0) {
                    continue;
                }

                $destino = $pick->productLocation;

                if ($destino) {
                    $destino->increment('cantidad', $pick->cantidad);
                } elseif ($pick->warehouse_location_id && $item->product_id) {
                    $destino = ProductLocation::create([
                        'product_id' => $item->product_id,
                        'warehouse_location_id' => $pick->warehouse_location_id,
                        'fecha_ingreso' => now()->toDateString(),
                        'cantidad' => $pick->cantidad,
                    ]);
                } else {
                    continue;
                }

                ProductLocationEvent::create([
                    'product_location_id' => $destino->id,
                    'user_id' => $userId,
                    'accion' => 'devuelta',
                    'detalle' => "{$item->producto_nombre}: +{$pick->cantidad} unidad(es) devueltas por cancelación de la orden #{$order->id}.",
                ]);

                $total += $pick->cantidad;

                // En cero para que una reversión nunca se aplique dos veces.
                $pick->update(['cantidad' => 0]);
            }
        }

        return $total;
    }
}
