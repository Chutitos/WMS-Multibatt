<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PreparationController extends Controller
{
    public function index()
    {
        $orders = Order::where('estado', 'liberado')->latest()->get();

        return view('warehouse.index', compact('orders'));
    }

    public function preparar(Request $request, Order $order)
    {
        if ($request->user()->role->name !== 'bodeguero') {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if ($order->estado !== 'liberado') {
            return redirect()->back()->with('error', 'Solo se pueden preparar órdenes liberadas.');
        }

        $order->update([
            'estado' => 'preparando',
        ]);

        return redirect()->route('bodega.preparando')
            ->with('success', 'Orden en preparación.');
    }

    public function preparando()
    {
        $orders = Order::where('estado', 'preparando')->latest()->get();

        return view('warehouse.preparando', compact('orders'));
    }

    public function confirmar(Request $request, Order $order)
    {
        if ($request->user()->role->name !== 'bodeguero') {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if ($order->estado !== 'preparando') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden confirmar órdenes en preparación.');
        }

        $order->update([
            'estado' => 'listo',
        ]);

        foreach ($order->items as $item) {
            $item->update([
                'cantidad_confirmada' => $item->cantidad_solicitada,
            ]);
        }

        return redirect()->route('bodega.listo')
            ->with('success', 'Productos confirmados correctamente.');
    }

    public function listo()
    {
        $orders = Order::where('estado', 'listo')->latest()->get();

        return view('warehouse.listo', compact('orders'));
    }

    public function entregar(Request $request, Order $order)
    {
        if ($request->user()->role->name !== 'bodeguero') {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if ($order->estado !== 'listo') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden entregar órdenes listas.');
        }

        $order->update([
            'estado' => 'entregado',
        ]);

        return redirect()->route('bodega.listo')
            ->with('success', 'Orden entregada correctamente.');
    }
}
