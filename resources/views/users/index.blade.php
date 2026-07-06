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
                    <th class="p-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach ($users as $user)
                <tr>
                    <td class="p-4 text-slate-900">{{ $user->name }}</td>
                    <td class="p-4 text-slate-900">{{ $user->email }}</td>
                    <td class="p-4 text-slate-900">{{ $user->role->name }}</td>
                    <td class="p-4 flex gap-2">

                        <a href="{{ route('users.edit', $user) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                            Editar
                        </a>

                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?')">
                            @csrf
                            @method('DELETE')

                            <button
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                                Eliminar
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection