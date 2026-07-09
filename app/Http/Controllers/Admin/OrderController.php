<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\Product;
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

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'estado' => OrderStatus::CANCELADO,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'cancelado',
                'descripcion' => 'Orden cancelada.',
                'user_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Orden cancelada correctamente.');
    }
}
