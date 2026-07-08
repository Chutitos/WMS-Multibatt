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
        class="inline-flex items-center px-6 py-4 bg-slate-200 text-slate-800 text-lg rounded-2xl font-semibold hover:bg-slate-300">
        ← Volver
    </a>

    @can('liberar', $order)
    <form method="POST" action="{{ route('orders.liberar', $order) }}">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-6 py-4 bg-blue-600 text-white text-lg rounded-2xl font-bold hover:bg-blue-700">
            Liberar orden
        </button>
    </form>
    @endcan

    @can('confirmar', $order)
    <a href="{{ route('orders.picking', $order) }}"
        class="inline-flex items-center px-6 py-4 bg-blue-600 text-white text-lg rounded-2xl font-bold hover:bg-blue-700">
        📷 Ir a escanear productos
    </a>
    @endcan

    @can('cancelar', $order)
    <form method="POST" action="{{ route('orders.cancelar', $order) }}"
        onsubmit="return confirm('¿Seguro que quieres cancelar esta orden? Esta acción no se puede deshacer.')">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-6 py-4 bg-red-600 text-white text-lg rounded-2xl font-bold hover:bg-red-700">
            Cancelar orden
        </button>
    </form>
    @endcan
</div>

@can('entregar', $order)
<div class="mt-6 max-w-xl bg-green-50 border-4 border-green-200 rounded-3xl p-6">
    <h3 class="text-xl font-bold text-green-900 mb-1">
        🤝 Entregar orden
    </h3>
    <p class="text-base text-slate-600 mb-4">
        {{ $order->tipo_entrega === 'retiro' ? 'El cliente retira en bodega. Anota quién se la lleva.' : 'Se despacha a domicilio. Anota el transportista.' }}
    </p>

    <form method="POST" action="{{ route('orders.entregar', $order) }}"
        onsubmit="return confirm('¿Confirmas que la orden fue entregada? Esta acción no se puede deshacer.')"
        class="space-y-4">
        @csrf
        @method('PATCH')

        @if ($order->tipo_entrega === 'retiro')
        <div>
            <label class="block text-base font-semibold text-slate-700 mb-2">Nombre de quién retira</label>
            <input type="text" name="retirado_por_nombre" value="{{ old('retirado_por_nombre') }}" required
                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-green-500 focus:ring-green-500">
            @error('retirado_por_nombre')
            <p class="mt-2 text-base text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-base font-semibold text-slate-700 mb-2">RUT (opcional)</label>
            <input type="text" name="retirado_por_rut" value="{{ old('retirado_por_rut') }}"
                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-green-500 focus:ring-green-500">
        </div>
        @else
        <div>
            <label class="block text-base font-semibold text-slate-700 mb-2">Transportista</label>
            <input type="text" name="transportista" value="{{ old('transportista') }}" required
                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-green-500 focus:ring-green-500">
            @error('transportista')
            <p class="mt-2 text-base text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-base font-semibold text-slate-700 mb-2">N° de guía (opcional)</label>
            <input type="text" name="guia_despacho" value="{{ old('guia_despacho') }}"
                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-green-500 focus:ring-green-500">
        </div>
        @endif

        <button type="submit"
            class="w-full px-6 py-5 bg-green-600 text-white text-xl font-bold rounded-2xl hover:bg-green-700 shadow-sm">
            🤝 Confirmar entrega
        </button>
    </form>
</div>
@endcan
@endsection