@php
use App\Enums\OrderStatus;
@endphp
@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 flex-wrap">
        <h2 class="text-3xl font-bold text-slate-900">Orden #{{ $order->id }}</h2>
        <x-order-status-badge :estado="$order->estado" class="text-sm px-4 py-1.5" />
    </div>
    <p class="mt-2 text-xl text-slate-700">
        {{ $order->cliente_nombre }}
        <span class="text-lg text-slate-500">
            · {{ $order->tipo_entrega === 'retiro' ? '🏬 Retiro en tienda' : '🚚 Despacho a domicilio' }}
        </span>
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Datos de la orden</h3>

        <dl class="space-y-3 text-base">
            <div class="flex justify-between gap-4">
                <dt class="font-semibold text-slate-600">RUT cliente</dt>
                <dd class="text-slate-900 text-right">{{ $order->rut_cliente ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="font-semibold text-slate-600">Creada por</dt>
                <dd class="text-slate-900 text-right">{{ $order->creator->name ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="font-semibold text-slate-600">Fecha creación</dt>
                <dd class="text-slate-900 text-right">{{ $order->created_at?->format('d-m-Y H:i') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="font-semibold text-slate-600">Enviada a bodega</dt>
                <dd class="text-slate-900 text-right">
                    {{ $order->fecha_liberacion ? $order->fecha_liberacion->format('d-m-Y H:i') : '—' }}
                </dd>
            </div>

            @if ($order->estado === OrderStatus::ENTREGADO)
                @if ($order->tipo_entrega === 'retiro')
                <div class="flex justify-between gap-4">
                    <dt class="font-semibold text-slate-600">Retirado por</dt>
                    <dd class="text-slate-900 text-right">
                        {{ $order->retirado_por_nombre }}
                        @if ($order->retirado_por_rut)
                        <span class="text-slate-500">({{ $order->retirado_por_rut }})</span>
                        @endif
                    </dd>
                </div>
                @else
                <div class="flex justify-between gap-4">
                    <dt class="font-semibold text-slate-600">Transportista</dt>
                    <dd class="text-slate-900 text-right">{{ $order->transportista }}</dd>
                </div>
                @if ($order->guia_despacho)
                <div class="flex justify-between gap-4">
                    <dt class="font-semibold text-slate-600">N° de guía</dt>
                    <dd class="text-slate-900 text-right">{{ $order->guia_despacho }}</dd>
                </div>
                @endif
                @endif
            @endif
        </dl>
    </div>

    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Observaciones</h3>
        <p class="text-base text-slate-700">
            {{ $order->observaciones ?: 'Sin observaciones.' }}
        </p>
    </div>
</div>

<div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h3 class="text-xl font-bold text-slate-900">Productos</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Producto</th>
                    <th class="px-6 py-4 text-center font-semibold text-slate-700">Pedidas</th>
                    <th class="px-6 py-4 text-center font-semibold text-slate-700">Confirmadas</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($order->items as $item)
                @php $itemCompleto = $item->cantidad_confirmada >= $item->cantidad_solicitada; @endphp
                <tr class="{{ $itemCompleto && $order->estado !== OrderStatus::CANCELADO ? 'bg-green-50/50' : '' }}">
                    <td class="px-6 py-4 text-slate-900 font-semibold">{{ $item->producto_nombre }}</td>
                    <td class="px-6 py-4 text-center text-slate-900 font-bold">{{ $item->cantidad_solicitada }}</td>
                    <td class="px-6 py-4 text-center font-bold {{ $itemCompleto ? 'text-green-600' : 'text-slate-900' }}">
                        {{ $item->cantidad_confirmada }}
                    </td>
                    <td class="px-6 py-4 text-green-600 font-semibold">
                        {{ $itemCompleto && $order->estado !== OrderStatus::CANCELADO ? '✅' : '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex items-center gap-3 flex-wrap">
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

    <x-wms.btn size="lg" variant="secondary" href="{{ route($volverRuta) }}">← Volver</x-wms.btn>

    <x-wms.btn size="lg" variant="secondary" href="{{ route('orders.imprimir', $order) }}" target="_blank">
        🖨 Imprimir
    </x-wms.btn>

    @can('liberar', $order)
    <form method="POST" action="{{ route('orders.liberar', $order) }}">
        @csrf
        @method('PATCH')
        <x-wms.btn size="lg">Enviar a bodega</x-wms.btn>
    </form>
    @endcan

    @can('confirmar', $order)
    <x-wms.btn size="lg" href="{{ route('orders.picking', $order) }}">📷 Ir a escanear productos</x-wms.btn>
    @endcan

    @can('cancelar', $order)
    <form method="POST" action="{{ route('orders.cancelar', $order) }}"
        onsubmit="return confirm('¿Seguro que quieres cancelar esta orden? Esta acción no se puede deshacer.')">
        @csrf
        @method('PATCH')
        <x-wms.btn size="lg" variant="danger">Cancelar orden</x-wms.btn>
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
        <x-wms.field label="Nombre de quién retira" name="retirado_por_nombre">
            <x-wms.input type="text" name="retirado_por_nombre" value="{{ old('retirado_por_nombre') }}" required />
        </x-wms.field>

        <x-wms.field label="RUT" name="retirado_por_rut" :optional="true">
            <x-wms.input type="text" name="retirado_por_rut" value="{{ old('retirado_por_rut') }}" />
        </x-wms.field>
        @else
        <x-wms.field label="Transportista" name="transportista">
            <x-wms.input type="text" name="transportista" value="{{ old('transportista') }}" required />
        </x-wms.field>

        <x-wms.field label="N° de guía" name="guia_despacho" :optional="true">
            <x-wms.input type="text" name="guia_despacho" value="{{ old('guia_despacho') }}" />
        </x-wms.field>
        @endif

        <x-wms.btn size="xl" variant="success">🤝 Confirmar entrega</x-wms.btn>
    </form>
</div>
@endcan

<div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h3 class="text-xl font-bold text-slate-900">Historial de la orden</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Evento</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Descripción</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($order->events as $event)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-slate-700 whitespace-nowrap">{{ $event->created_at->format('d-m-Y H:i') }}</td>
                    <td class="px-6 py-4 capitalize text-slate-900 font-semibold">{{ $event->tipo_evento }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $event->descripcion }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $event->user->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">Sin eventos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
