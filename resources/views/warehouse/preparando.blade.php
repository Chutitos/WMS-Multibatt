@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xl">2</span>
        <h2 class="text-3xl font-bold text-slate-900">Preparando</h2>
    </div>
    <p class="mt-3 text-lg text-slate-600">
        Escanea los productos de cada orden con la pistola. Cuando esté completa, confírmala.
    </p>
</div>

<div class="space-y-4">
    @forelse ($orders as $order)
    @php
    $total = (int) ($order->total_solicitado ?? 0);
    $confirmado = (int) ($order->total_confirmado ?? 0);
    $completa = $total > 0 && $confirmado >= $total;
    $porcentaje = $total > 0 ? intval($confirmado / $total * 100) : 0;
    @endphp
    <div class="bg-white border-2 {{ $completa ? 'border-green-300' : 'border-slate-200' }} rounded-2xl p-6 flex flex-col md:flex-row md:items-center gap-5 transition">
        <div class="flex-1">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-2xl font-bold text-slate-900">Orden #{{ $order->id }}</span>
                <x-order-status-badge :estado="$order->estado" />
            </div>
            <div class="mt-2 text-xl text-slate-800">{{ $order->cliente_nombre }}</div>

            <div class="mt-3">
                <div class="flex items-center justify-between text-lg">
                    <span class="font-semibold {{ $completa ? 'text-green-700' : 'text-slate-700' }}">
                        {{ $completa ? '✅ Escaneo completo' : "Escaneados {$confirmado} de {$total}" }}
                    </span>
                    <span class="text-slate-500">{{ $porcentaje }}%</span>
                </div>
                <div class="mt-2 h-4 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $completa ? 'bg-green-500' : 'bg-orange-400' }}" style="width: {{ $porcentaje }}%"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 md:w-80">
            @if ($completa)
            <form method="POST" action="{{ route('orders.confirmar', $order) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="w-full px-6 py-5 bg-green-600 text-white text-xl font-bold rounded-2xl hover:bg-green-700 shadow-sm">
                    ✅ Confirmar que está lista
                </button>
            </form>
            @else
            <a href="{{ route('orders.picking', $order) }}"
                class="w-full text-center px-6 py-5 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 shadow-sm">
                📷 Escanear productos
            </a>
            @endif

            <a href="{{ route('orders.show', $order) }}"
                class="w-full text-center px-6 py-3 bg-slate-100 border border-slate-300 text-slate-700 text-lg font-semibold rounded-2xl hover:bg-slate-200">
                Ver detalle
            </a>
        </div>
    </div>
    @empty
    <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl py-16 text-center">
        <div class="text-5xl">📦</div>
        <p class="mt-4 text-2xl font-bold text-slate-700">No hay órdenes en preparación</p>
        <p class="mt-2 text-lg text-slate-500">Toma una desde <a href="{{ route('bodega.index') }}" class="text-blue-600 underline font-semibold">Por preparar</a>.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
@endsection
