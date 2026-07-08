@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xl">1</span>
        <h2 class="text-3xl font-bold text-slate-900">Por preparar</h2>
    </div>
    <p class="mt-3 text-lg text-slate-600">
        Elige una orden y toca <strong>"Comenzar a preparar"</strong>.
    </p>
</div>

<div class="space-y-4">
    @forelse ($orders as $order)
    <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 flex flex-col md:flex-row md:items-center gap-5 hover:border-blue-300 transition">
        <div class="flex-1">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-2xl font-bold text-slate-900">Orden #{{ $order->id }}</span>
                <x-order-status-badge :estado="$order->estado" />
            </div>
            <div class="mt-2 text-xl text-slate-800">{{ $order->cliente_nombre }}</div>
            <div class="mt-1 text-lg text-slate-600">
                {{ $order->tipo_entrega === 'retiro' ? '🏬 Retiro en tienda' : '🚚 Despacho a domicilio' }}
                &nbsp;·&nbsp; {{ $order->items_count }} {{ $order->items_count === 1 ? 'producto' : 'productos' }}
            </div>
        </div>

        <div class="flex flex-col gap-3 md:w-80">
            <form method="POST" action="{{ route('orders.preparar', $order) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="w-full px-6 py-5 bg-green-600 text-white text-xl font-bold rounded-2xl hover:bg-green-700 shadow-sm">
                    ▶ Comenzar a preparar
                </button>
            </form>

            <a href="{{ route('orders.show', $order) }}"
                class="w-full text-center px-6 py-3 bg-slate-100 border border-slate-300 text-slate-700 text-lg font-semibold rounded-2xl hover:bg-slate-200">
                Ver detalle
            </a>
        </div>
    </div>
    @empty
    <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl py-16 text-center">
        <div class="text-5xl">✅</div>
        <p class="mt-4 text-2xl font-bold text-slate-700">No hay órdenes por preparar</p>
        <p class="mt-2 text-lg text-slate-500">Cuando liberen una orden nueva, aparecerá aquí.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
