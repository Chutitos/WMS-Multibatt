@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Historial de existencias" subtitle="Quién creó, editó o eliminó cada registro de existencia y qué cambió.">
    <x-slot:actions>
        <x-wms.btn variant="secondary" href="{{ route('product-locations.index') }}">Volver a existencias</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acción</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Usuario</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($eventos as $evento)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                        {{ $evento->created_at->format('d-m-Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $badge = match ($evento->accion) {
                            'creada' => 'bg-green-100 text-green-800',
                            'editada' => 'bg-orange-100 text-orange-800',
                            'eliminada' => 'bg-red-100 text-red-800',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                            {{ ucfirst($evento->accion) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-900 font-semibold">
                        {{ $evento->user?->name ?? 'Usuario eliminado' }}
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $evento->detalle }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-4xl">📒</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">Todavía no hay movimientos registrados</p>
                        <p class="mt-1 text-base text-slate-500">Cada vez que alguien cree, edite o elimine una existencia quedará anotado aquí.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $eventos->links() }}
</div>
@endsection
