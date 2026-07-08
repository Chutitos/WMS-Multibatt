@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Usuarios" subtitle="Administración de usuarios y roles.">
    <x-slot:actions>
        <x-wms.btn href="{{ route('users.create') }}">+ Crear usuario</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Nombre</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Email</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Rol</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @foreach ($users as $user)
                <tr class="hover:bg-slate-50 {{ $user->activo ? '' : 'opacity-60' }}">
                    <td class="px-6 py-4 text-slate-900 font-semibold">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $user->role->name }}</td>
                    <td class="px-6 py-4">
                        <x-wms.badge-activo :activo="$user->activo" />
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <x-wms.btn size="sm" href="{{ route('users.edit', $user) }}">Editar</x-wms.btn>

                            @unless (auth()->id() === $user->id)
                            <form method="POST" action="{{ route('users.toggle-activo', $user) }}"
                                onsubmit="return confirm('{{ $user->activo ? '¿Desactivar este usuario? No podrá iniciar sesión.' : '¿Reactivar este usuario?' }}')">
                                @csrf
                                @method('PATCH')
                                <x-wms.btn size="sm" :variant="$user->activo ? 'danger' : 'success'">
                                    {{ $user->activo ? 'Desactivar' : 'Activar' }}
                                </x-wms.btn>
                            </form>
                            @endunless
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
