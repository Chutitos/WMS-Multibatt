<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntregarOrderRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreparationController extends Controller
{
    public function index()
    {
        $orders = Order::where('estado', OrderStatus::LIBERADO)
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('warehouse.index', compact('orders'));
    }

    public function preparar(Request $request, Order $order)
    {
        if (! $order->estado->canTransitionTo(OrderStatus::PREPARANDO)) {
            return redirect()->back()->with('error', 'Solo se pueden preparar órdenes liberadas.');
        }

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'estado' => OrderStatus::PREPARANDO,
            ]);

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'preparando',
                'descripcion' => 'Orden en preparación.',
                'user_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('bodega.preparando')
            ->with('success', 'Orden en preparación. Confírmala cuando termines de prepararla.');
    }

    public function preparando()
    {
        // Totales para mostrar el avance del escaneo en cada tarjeta.
        $orders = Order::where('estado', OrderStatus::PREPARANDO)
            ->withSum('items as total_solicitado', 'cantidad_solicitada')
            ->withSum('items as total_confirmado', 'cantidad_confirmada')
            ->latest()
            ->paginate(15);

        return view('warehouse.preparando', compact('orders'));
    }

    public function confirmar(Request $request, Order $order)
    {
        if (! $order->estado->canTransitionTo(OrderStatus::LISTO)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden confirmar órdenes en preparación.');
        }

        // Los items con product_id deben confirmarse escaneando (picking con
        // pistola); solo los items antiguos en texto libre (sin catálogo) se
        // completan directamente para no romper órdenes ya creadas.
        $pendienteDeEscaneo = $order->items()
            ->whereNotNull('product_id')
            ->whereColumn('cantidad_confirmada', '<', 'cantidad_solicitada')
            ->exists();

        if ($pendienteDeEscaneo) {
            return redirect()->route('orders.picking', $order)
                ->with('error', 'Todavía hay productos del catálogo sin escanear por completo.');
        }

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'estado' => OrderStatus::LISTO,
            ]);

            foreach ($order->items()->whereNull('product_id')->get() as $item) {
                $item->update([
                    'cantidad_confirmada' => $item->cantidad_solicitada,
                ]);
            }

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'listo',
                'descripcion' => 'Productos confirmados.',
                'user_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('bodega.listo')
            ->with('success', 'Productos confirmados correctamente. La orden ya está lista para entregar.');
    }

    public function listo()
    {
        $orders = Order::where('estado', OrderStatus::LISTO)->latest()->paginate(15);

        return view('warehouse.listo', compact('orders'));
    }

    public function entregar(EntregarOrderRequest $request, Order $order)
    {
        if (! $order->estado->canTransitionTo(OrderStatus::ENTREGADO)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden entregar órdenes listas.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($request, $order, $validated) {
            $order->update(array_merge($validated, [
                'estado' => OrderStatus::ENTREGADO,
            ]));

            $descripcion = $order->tipo_entrega === 'retiro'
                ? "Orden entregada. Retirada por {$validated['retirado_por_nombre']}."
                : "Orden despachada vía {$validated['transportista']}"
                    . (! empty($validated['guia_despacho']) ? " (guía {$validated['guia_despacho']})" : '') . '.';

            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => 'entregado',
                'descripcion' => $descripcion,
                'user_id' => $request->user()->id,
            ]);
        });

        return redirect()->route('bodega.listo')
            ->with('success', 'Orden entregada correctamente.');
    }
}
