@php
use App\Enums\OrderStatus;
@endphp
@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Órdenes" subtitle="Gestión y seguimiento de órdenes operativas.">
    <x-slot:actions>
        <x-wms.btn href="{{ route('orders.create') }}">+ Crear orden</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<form method="GET" action="{{ route('orders.index') }}"
    class="mb-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Estado</label>
        <select name="estado" class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
            <option value="">Todos</option>
            @foreach (OrderStatus::cases() as $status)
            <option value="{{ $status->value }}" {{ request('estado') === $status->value ? 'selected' : '' }}>
                {{ $status->label() }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Cliente</label>
        <input type="text" name="cliente" value="{{ request('cliente') }}" placeholder="Nombre del cliente"
            class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Desde</label>
        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
            class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
            class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div class="flex gap-2">
        <x-wms.btn variant="dark">Filtrar</x-wms.btn>

        @if (request()->filled('estado') || request()->filled('cliente') || request()->filled('fecha_desde') || request()->filled('fecha_hasta'))
        <x-wms.btn variant="secondary" href="{{ route('orders.index') }}">Limpiar</x-wms.btn>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
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
                    <td class="px-6 py-4 font-bold text-slate-900">#{{ $order->id }}</td>
                    <td class="px-6 py-4 text-slate-900">{{ $order->cliente_nombre }}</td>
                    <td class="px-6 py-4 text-slate-700">
                        {{ $order->tipo_entrega === 'retiro' ? '🏬 Retiro' : '🚚 Despacho' }}
                    </td>
                    <td class="px-6 py-4">
                        <x-order-status-badge :estado="$order->estado" />
                    </td>
                    <td class="px-6 py-4 capitalize text-slate-700">{{ $order->source_type }}</td>
                    <td class="px-6 py-4">
                        <x-wms.btn size="sm" href="{{ route('orders.show', $order) }}">Ver</x-wms.btn>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-4xl">📋</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">No hay órdenes con estos filtros</p>
                        <p class="mt-1 text-base text-slate-500">Prueba limpiando los filtros o crea una orden nueva.</p>
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
