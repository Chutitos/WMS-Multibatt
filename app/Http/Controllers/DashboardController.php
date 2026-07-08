<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role->name, ['admin', 'jefe_bodega'], true)) {
            $counts = [
                'creadas' => Order::where('estado', OrderStatus::CREADO)->count(),
                'liberadas' => Order::where('estado', OrderStatus::LIBERADO)->count(),
                'preparando' => Order::where('estado', OrderStatus::PREPARANDO)->count(),
                'listas' => Order::where('estado', OrderStatus::LISTO)->count(),
                'entregadas' => Order::where('estado', OrderStatus::ENTREGADO)->count(),
                'canceladas' => Order::where('estado', OrderStatus::CANCELADO)->count(),
            ];

            return view('dashboard.jefe', compact('counts'));
        }

        if ($user->role->name === 'bodeguero') {
            $counts = [
                'liberadas' => Order::where('estado', OrderStatus::LIBERADO)->count(),
                'preparando' => Order::where('estado', OrderStatus::PREPARANDO)->count(),
                'listas' => Order::where('estado', OrderStatus::LISTO)->count(),
            ];

            return view('dashboard.bodega', compact('counts'));
        }

        abort(403);
    }
}
