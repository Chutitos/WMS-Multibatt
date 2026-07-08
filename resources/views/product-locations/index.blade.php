@extends('layouts.wms')

@php
$esAdmin = auth()->user()->role->name === 'admin';
@endphp

@section('content')
<x-wms.page-header title="Existencias por ubicación" subtitle="Qué producto hay guardado en cada ubicación de la bodega, ordenado por antigüedad (FIFO).">
    <x-slot:actions>
        @if ($esAdmin)
        <x-wms.btn variant="secondary" href="{{ route('product-locations.historial') }}">Historial</x-wms.btn>
        @endif
        <x-wms.btn href="{{ route('product-locations.create') }}">+ Asignar existencia</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Producto</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Ubicación</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Puesto</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Lote</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha ingreso</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cantidad</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($productLocations as $pl)
                <tr class="hover:bg-slate-50 {{ $pl->cantidad === 0 ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4 text-slate-900 font-semibold">{{ $pl->product->name }}</td>
                    <td class="px-6 py-4 text-slate-900">
                        {{ $pl->warehouseLocation->nombre }}
                        <span class="text-slate-500 font-mono text-sm">({{ $pl->warehouseLocation->codigo }})</span>
                    </td>
                    <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                        {{ $pl->columna ? "C{$pl->columna} · N{$pl->nivel}" : '-' }}
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $pl->lote ?: '-' }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $pl->fecha_ingreso->format('d-m-Y') }}</td>
                    <td class="px-6 py-4 text-slate-900 font-bold">
                        {{ $pl->cantidad }}
                        @if ($pl->cantidad === 0)
                        <span class="ml-1 text-sm font-normal text-slate-500">(agotado)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <x-wms.btn size="sm" href="{{ route('product-locations.edit', $pl) }}">Editar</x-wms.btn>

                            @if ($esAdmin)
                            <form method="POST" action="{{ route('product-locations.destroy', $pl) }}"
                                onsubmit="return confirm('¿Eliminar este registro de existencia? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <x-wms.btn size="sm" variant="danger">Eliminar</x-wms.btn>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="text-4xl">🗄️</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">No hay existencias asignadas todavía</p>
                        <p class="mt-1 text-base text-slate-500">Usa "+ Asignar existencia" para registrar qué hay en cada estante.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $productLocations->links() }}
</div>
@endsection
