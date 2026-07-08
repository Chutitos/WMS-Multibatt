@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Editar usuario" subtitle="Modifica los datos del usuario." />

<x-wms.errors />

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <x-wms.field label="Nombre" name="name">
            <x-wms.input type="text" name="name" value="{{ old('name', $user->name) }}" required />
        </x-wms.field>

        <x-wms.field label="Email" name="email">
            <x-wms.input type="email" name="email" value="{{ old('email', $user->email) }}" required />
        </x-wms.field>

        <x-wms.field label="Nueva contraseña" name="password" :optional="true" hint="Déjala vacía para mantener la actual.">
            <x-wms.input type="password" name="password" />
        </x-wms.field>

        <x-wms.field label="Rol" name="role_id">
            <x-wms.select name="role_id" required>
                @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </x-wms.select>
        </x-wms.field>

        <div class="pt-4 flex items-center gap-3">
            <x-wms.btn variant="success">Guardar cambios</x-wms.btn>
            <x-wms.btn variant="secondary" href="{{ route('users.index') }}">Volver</x-wms.btn>
        </div>
    </form>
</div>
@endsection
