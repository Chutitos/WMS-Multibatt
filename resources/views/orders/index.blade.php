@php
use App\Enums\OrderStatus;
@endphp
@extends('layouts.wms')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-3xl font-bold text-slate-900">Órdenes</h2>
        <p class="mt-2 text-sm text-slate-600">
            Gestión y seguimiento de órdenes operativas.
        </p>
    </div>

    <a href="{{ route('orders.create') }}"
        class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
        Crear orden
    </a>
</div>

<form method="GET" action="{{ route('orders.index') }}"
    class="mb-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
        <select name="estado" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Todos</option>
            @foreach (OrderStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ request('estado') === $status->value ? 'selected' : '' }}>
                {{ $status->label() }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Cliente</label>
        <input type="text" name="cliente" value="{{ request('cliente') }}" placeholder="Nombre del cliente"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Desde</label>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div class="flex gap-2">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-900">
            Filtrar
        </button>

        @if (request()->filled('estado') || request()->filled('cliente') || request()->filled('fecha_desde') || request()->filled('fecha_hasta'))
        <a href="{{ route('orders.index') }}"
            class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300">
            Limpiar
        </a>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">ID</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cliente</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Tipo entrega</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Origen</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium text-slate-900">
                        #{{ $order->id }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $order->cliente_nombre }}
                    </td>

                    <td class="px-6 py-4 capitalize">
                        {{ $order->tipo_entrega }}
                    </td>

                    <td class="px-6 py-4">
                        <x-order-status-badge :estado="$order->estado" />
                    </td>

                    <td class="px-6 py-4">
                        {{ $order->source_type }}
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ route('orders.show', $order) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                        No hay órdenes registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection