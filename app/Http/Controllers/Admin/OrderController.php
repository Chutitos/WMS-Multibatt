<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'rut_cliente' => 'nullable|string|max:20',
            'tipo_entrega' => 'required|string',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.nombre' => 'required|string|max:255',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'source_type' => 'manual',
                'source_reference' => null,
                'cliente_nombre' => $validated['cliente_nombre'],
                'rut_cliente' => $validated['rut_cliente'] ?? null,
                'tipo_entrega' => $validated['tipo_entrega'],
                'estado' => 'creado',
                'observaciones' => $validated['observaciones'] ?? null,
                'creado_por' => $request->user()->id,
                'liberado_por' => null,
                'fecha_liberacion' => null,
            ]);

            foreach ($validated['productos'] as $producto) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'producto_codigo' => $producto['nombre'],
                    'producto_nombre' => $producto['nombre'],
                    'cantidad_solicitada' => $producto['cantidad'],
                    'cantidad_confirmada' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Orden creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error al crear la orden.');
        }
    }

    public function show(Order $order)
    {
        $order->load('items', 'creator', 'releaser');

        return view('orders.show', compact('order'));
    }

    public function liberar(Request $request, Order $order)
    {
        if ($request->user()->role->name !== 'jefe_bodega') {
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if ($order->estado !== 'creado') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo se pueden liberar órdenes en estado creado.');
        }

        $order->update([
            'estado' => 'liberado',
            'liberado_por' => $request->user()->id,
            'fecha_liberacion' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Orden liberada correctamente.');
    }
}
