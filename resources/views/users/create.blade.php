@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Crear usuario</h2>
    <p class="mt-2 text-sm text-slate-600">
        Registra un nuevo usuario y asigna su rol.
    </p>
</div>

@if ($errors->any())
<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
    <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Contraseña</label>
            <input
                type="password"
                name="password"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('password')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Rol</label>
            <select
                name="role_id"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
                <option value="">Seleccione un rol</option>
                @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>
            @error('role_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button
                type="submit"
                class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                Guardar usuario
            </button>

            <a
                href="{{ route('users.index') }}"
                class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
                Volver
            </a>
        </div>
    </form>
</div>
@endsection