@extends('layouts.wms')

@php
$esAdmin = auth()->user()->role->name === 'admin';
@endphp

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Existencias por ubicación</h2>
        <p class="mt-2 text-sm text-slate-600">
            Qué producto hay guardado en cada ubicación de la bodega, ordenado por antigüedad (FIFO).
        </p>
    </div>

    <div class="flex items-center gap-3">
        @if ($esAdmin)
        <a href="{{ route('product-locations.historial') }}"
            class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
            Historial
        </a>
        @endif

        <a href="{{ route('product-locations.create') }}"
            class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
            Asignar existencia
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Producto</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Ubicación</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Lote</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha ingreso</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cantidad</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($productLocations as $pl)
                <tr class="hover:bg-slate-50 {{ $pl->cantidad === 0 ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4 text-slate-900">{{ $pl->product->name }}</td>
                    <td class="px-6 py-4 text-slate-900">
                        {{ $pl->warehouseLocation->nombre }}
                        <span class="text-slate-500 font-mono text-xs">({{ $pl->warehouseLocation->codigo }})</span>
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $pl->lote ?: '-' }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $pl->fecha_ingreso->format('d-m-Y') }}</td>
                    <td class="px-6 py-4 text-slate-900 font-semibold">
                        {{ $pl->cantidad }}
                        @if ($pl->cantidad === 0)
                        <span class="ml-1 text-xs font-normal text-slate-500">(agotado)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('product-locations.edit', $pl) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                            Editar
                        </a>
                        @if ($esAdmin)
                        <form method="POST" action="{{ route('product-locations.destroy', $pl) }}"
                            onsubmit="return confirm('¿Eliminar este registro de existencia? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                        No hay existencias asignadas todavía.
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
