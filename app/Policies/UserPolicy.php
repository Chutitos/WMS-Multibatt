<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Autoriza el cambio de rol específicamente: evita que el último admin
     * activo del sistema pierda ese rol (por sí mismo o por otro admin),
     * lo que dejaría el sistema sin nadie que administre usuarios.
     */
    public function changeRole(User $user, User $model, int $nuevoRoleId): bool
    {
        if ($user->role->name !== 'admin') {
            return false;
        }

        $dejaDeSerAdmin = $model->role->name === 'admin' && $model->role_id !== $nuevoRoleId;

        if ($dejaDeSerAdmin && $model->activo && $this->esUltimoAdminActivo($model)) {
            return false;
        }

        return true;
    }

    public function toggleActivo(User $user, User $model): bool
    {
        if ($user->role->name !== 'admin') {
            return false;
        }

        if ($user->is($model)) {
            return false;
        }

        // Solo bloquea al desactivar: si ya está inactivo, reactivarlo nunca
        // reduce la cantidad de admins activos.
        if ($model->activo && $model->role->name === 'admin' && $this->esUltimoAdminActivo($model)) {
            return false;
        }

        return true;
    }

    private function esUltimoAdminActivo(User $model): bool
    {
        return User::whereHas('role', fn ($query) => $query->where('name', 'admin'))
            ->where('activo', true)
            ->count() <= 1;
    }
}
