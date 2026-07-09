<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductLocation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Alertas propias de una bodega de baterías: pallets que llevan
        // demasiado tiempo almacenados (necesitan recarga) y baterías
        // cuya existencia física cayó bajo su mínimo.
        $porRecargar = ProductLocation::with('product')
            ->where('cantidad', '>', 0)
            ->get()
            ->filter(fn ($pl) => $pl->necesitaRecarga())
            ->count();

        $bajoStock = Product::where('active', true)
            ->where('stock_minimo', '>', 0)
            ->withSum('productLocations as existencia_fisica', 'cantidad')
            ->get()
            ->filter(fn ($p) => (int) ($p->existencia_fisica ?? 0) < $p->stock_minimo)
            ->count();

        if (in_array($user->role->name, ['admin', 'jefe_bodega'], true)) {
            $counts = [
                'liberadas' => Order::where('estado', OrderStatus::LIBERADO)->count(),
                'preparando' => Order::where('estado', OrderStatus::PREPARANDO)->count(),
                'listas' => Order::where('estado', OrderStatus::LISTO)->count(),
                'entregadas' => Order::where('estado', OrderStatus::ENTREGADO)->count(),
                'canceladas' => Order::where('estado', OrderStatus::CANCELADO)->count(),
            ];

            return view('dashboard.jefe', compact('counts', 'porRecargar', 'bajoStock'));
        }

        if ($user->role->name === 'bodeguero') {
            $counts = [
                'liberadas' => Order::where('estado', OrderStatus::LIBERADO)->count(),
                'preparando' => Order::where('estado', OrderStatus::PREPARANDO)->count(),
                'listas' => Order::where('estado', OrderStatus::LISTO)->count(),
            ];

            return view('dashboard.bodega', compact('counts', 'porRecargar', 'bajoStock'));
        }

        abort(403);
    }
}
