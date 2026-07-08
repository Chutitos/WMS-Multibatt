@extends('layouts.wms')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Usuarios</h2>
        <p class="mt-2 text-sm text-slate-600">
            Administración de usuarios y roles.
        </p>
    </div>

    <a href="{{ route('users.create') }}"
        class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
        Crear usuario
    </a>
</div>

<div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-300">
                <tr>
                    <th class="p-4 text-left font-semibold text-slate-700">Nombre</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Email</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Rol</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach ($users as $user)
                <tr>
                    <td class="p-4 text-slate-900">{{ $user->name }}</td>
                    <td class="p-4 text-slate-900">{{ $user->email }}</td>
                    <td class="p-4 text-slate-900">{{ $user->role->name }}</td>
                    <td class="p-4">
                        @if ($user->activo)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Activo</span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Inactivo</span>
                        @endif
                    </td>
                    <td class="p-4 flex gap-2">

                        <a href="{{ route('users.edit', $user) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                            Editar
                        </a>

                        @unless (auth()->id() === $user->id)
                        <form method="POST" action="{{ route('users.toggle-activo', $user) }}"
                            onsubmit="return confirm('{{ $user->activo ? '¿Desactivar este usuario? No podrá iniciar sesión.' : '¿Reactivar este usuario?' }}')">
                            @csrf
                            @method('PATCH')

                            <button
                                class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-white {{ $user->activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                {{ $user->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        @endunless

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection