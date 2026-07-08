@php
use App\Enums\OrderStatus;
@endphp
@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-slate-900">Orden #{{ $order->id }}</h2>
    <p class="mt-2 text-sm text-slate-600">
        Detalle completo de la orden operativa.
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Datos generales</h3>

        <div class="space-y-3 text-sm">
            <div>
                <span class="font-semibold text-slate-700">Cliente:</span>
                <span class="text-slate-900">{{ $order->cliente_nombre }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">RUT:</span>
                <span class="text-slate-900">{{ $order->rut_cliente ?: '-' }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">Tipo de entrega:</span>
                <span class="text-slate-900 capitalize">{{ $order->tipo_entrega }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">Estado:</span>
<x-order-status-badge :estado="$order->estado" />
            </div>

            <div>
                <span class="font-semibold text-slate-700">Creada por:</span>
                <span class="text-slate-900">{{ $order->creator->name ?? '-' }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">Fecha creación:</span>
                <span class="text-slate-900">{{ $order->created_at?->format('d-m-Y H:i') }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">Liberada por:</span>
                <span class="text-slate-900">{{ $order->releaser->name ?? '-' }}</span>
            </div>

            <div>
                <span class="font-semibold text-slate-700">Fecha liberación:</span>
                <span class="text-slate-900">
                    {{ $order->fecha_liberacion ? $order->fecha_liberacion->format('d-m-Y H:i') : '-' }}
                </span>
            </div>

            @if ($order->estado === OrderStatus::ENTREGADO)
                @if ($order->tipo_entrega === 'retiro')
                <div>
                    <span class="font-semibold text-slate-700">Retirado por:</span>
                    <span class="text-slate-900">{{ $order->retirado_por_nombre }}</span>
                    @if ($order->retirado_por_rut)
                    <span class="text-slate-500">({{ $order->retirado_por_rut }})</span>
                    @endif
                </div>
                @else
                <div>
                    <span class="font-semibold text-slate-700">Transportista:</span>
                    <span class="text-slate-900">{{ $order->transportista }}</span>
                </div>
                @if ($order->guia_despacho)
                <div>
                    <span class="font-semibold text-slate-700">N° de guía:</span>
                    <span class="text-slate-900">{{ $order->guia_despacho }}</span>
                </div>
                @endif
                @endif
            @endif
        </div>
    </div>

    <div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Observaciones</h3>

        <div class="text-sm text-slate-700 min-h-[120px]">
            {{ $order->observaciones ?: 'Sin observaciones.' }}
        </div>
    </div>
</div>

<div class="mt-6 bg-slate-50 rounded-2xl shadow-sm border border-slate-300 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-300">
        <h3 class="text-lg font-semibold text-slate-900">Productos</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-300">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Producto</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cantidad solicitada</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cantidad confirmada</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($order->items as $item)
                <tr>
                    <td class="px-6 py-4 text-slate-900">{{ $item->producto_nombre }}</td>
                    <td class="px-6 py-4 text-slate-900">{{ $item->cantidad_solicitada }}</td>
                    <td class="px-6 py-4 text-slate-900">{{ $item->cantidad_confirmada }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 bg-slate-50 rounded-2xl shadow-sm border border-slate-300 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-300">
        <h3 class="text-lg font-semibold text-slate-900">Historial de eventos</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-300">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Evento</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Descripción</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Usuario</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($order->events as $event)
                <tr>
                    <td class="px-6 py-4 capitalize text-slate-900">{{ $event->tipo_evento }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $event->descripcion }}</td>
                    <td class="px-6 py-4 text-slate-900">{{ $event->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $event->created_at->format('d-m-Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-slate-500">
                        Sin eventos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    @php
        $volverRuta = 'orders.index';

        if (auth()->user()->role->name === 'bodeguero') {
            $volverRuta = match ($order->estado) {
                OrderStatus::LIBERADO => 'bodega.index',
                OrderStatus::PREPARANDO => 'bodega.preparando',
                OrderStatus::LISTO, OrderStatus::ENTREGADO => 'bodega.listo',
                default => 'bodega.index',
            };
        }
    @endphp

    <a href="{{ route($volverRuta) }}"
        class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
        Volver
    </a>

    @can('liberar', $order)
    <form method="POST" action="{{ route('orders.liberar', $order) }}">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
            Liberar orden
        </button>
    </form>
    @endcan

    @can('confirmar', $order)
    <a href="{{ route('orders.picking', $order) }}"
        class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
        Ir a picking / confirmar productos
    </a>
    @endcan

    @can('cancelar', $order)
    <form method="POST" action="{{ route('orders.cancelar', $order) }}"
        onsubmit="return confirm('¿Seguro que quieres cancelar esta orden? Esta acción no se puede deshacer.')">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700">
            Cancelar orden
        </button>
    </form>
    @endcan
</div>

@can('entregar', $order)
<div class="mt-4 max-w-md bg-slate-50 border border-slate-200 rounded-2xl p-5">
    <h3 class="text-sm font-semibold text-slate-900 mb-3">
        Entregar orden
        <span class="font-normal text-slate-500">({{ $order->tipo_entrega === 'retiro' ? 'Retiro en bodega' : 'Despacho' }})</span>
    </h3>

    <form method="POST" action="{{ route('orders.entregar', $order) }}"
        onsubmit="return confirm('¿Confirmas que la orden fue entregada? Esta acción no se puede deshacer.')"
        class="space-y-3">
        @csrf
        @method('PATCH')

        @if ($order->tipo_entrega === 'retiro')
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre de quién retira</label>
            <input type="text" name="retirado_por_nombre" value="{{ old('retirado_por_nombre') }}" required
                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('retirado_por_nombre')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">RUT (opcional)</label>
            <input type="text" name="retirado_por_rut" value="{{ old('retirado_por_rut') }}"
                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        @else
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Transportista</label>
            <input type="text" name="transportista" value="{{ old('transportista') }}" required
                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('transportista')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">N° de guía (opcional)</label>
            <input type="text" name="guia_despacho" value="{{ old('guia_despacho') }}"
                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        @endif

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700">
            Confirmar entrega
        </button>
    </form>
</div>
@endcan
@endsection