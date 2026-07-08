<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'jefe_bodega'], true);
    }

    /**
     * Bodeguero solo puede ver el detalle de órdenes que ya salieron de manos
     * del jefe de bodega (todo lo que no esté en estado "creado").
     */
    public function view(User $user, Order $order): bool
    {
        return match ($user->role->name) {
            'admin', 'jefe_bodega' => true,
            'bodeguero' => $order->estado !== OrderStatus::CREADO,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'jefe_bodega'], true);
    }

    public function liberar(User $user, Order $order): bool
    {
        return in_array($user->role->name, ['admin', 'jefe_bodega'], true)
            && $order->estado->canTransitionTo(OrderStatus::LIBERADO);
    }

    public function preparar(User $user, Order $order): bool
    {
        return in_array($user->role->name, ['admin', 'bodeguero'], true)
            && $order->estado->canTransitionTo(OrderStatus::PREPARANDO);
    }

    public function confirmar(User $user, Order $order): bool
    {
        return in_array($user->role->name, ['admin', 'bodeguero'], true)
            && $order->estado->canTransitionTo(OrderStatus::LISTO);
    }

    public function entregar(User $user, Order $order): bool
    {
        return in_array($user->role->name, ['admin', 'bodeguero'], true)
            && $order->estado->canTransitionTo(OrderStatus::ENTREGADO);
    }

    public function cancelar(User $user, Order $order): bool
    {
        return $order->canBeCancelledBy($user);
    }
}
