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
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                    {{ $order->estado }}
                </span>
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

<div class="mt-6 flex items-center gap-3">
    <a href="{{ route('orders.index') }}"
        class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
        Volver
    </a>

    @if ($order->estado === 'creado' && auth()->user()->role->name === 'jefe_bodega')
    <form method="POST" action="{{ route('orders.liberar', $order) }}">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
            Liberar orden
        </button>
    </form>
    @endif

    @if ($order->estado === 'preparando' && auth()->user()->role->name === 'bodeguero')
    <form method="POST" action="{{ route('orders.confirmar', $order) }}">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
            Confirmar productos
        </button>
    </form>
    @endif

    @if ($order->estado === 'listo' && auth()->user()->role->name === 'bodeguero')
    <form method="POST" action="{{ route('orders.entregar', $order) }}">
        @csrf
        @method('PATCH')

        <button type="submit"
            class="inline-flex items-center px-5 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700">
            Entregar orden
        </button>
    </form>
    @endif
</div>
@endsection