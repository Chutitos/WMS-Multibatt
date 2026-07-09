@extends('layouts.wms')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-blue-900">Hola, {{ auth()->user()->name }}</h2>
    <p class="mt-2 text-lg text-slate-600">
        Así está la operación ahora. Toca una tarjeta para ver esas órdenes.
    </p>
</div>

@php $esAdmin = auth()->user()->role->name === 'admin'; @endphp

@if ($bajoStock > 0)
<div class="mb-6 space-y-3">
    @if ($bajoStock > 0)
    <div class="bg-red-50 border-4 border-red-200 rounded-2xl px-6 py-4 {{ $esAdmin ? 'hover:bg-red-100 transition' : '' }}">
        @if ($esAdmin)
        <a href="{{ route('products.index') }}" class="block">
            <span class="text-xl font-bold text-red-900">🔋 {{ $bajoStock }} {{ $bajoStock === 1 ? 'batería está' : 'baterías están' }} bajo el stock mínimo</span>
            <span class="block mt-1 text-base text-red-800">Revisa el catálogo para ver cuáles reponer.</span>
        </a>
        @else
        <span class="text-xl font-bold text-red-900">🔋 {{ $bajoStock }} {{ $bajoStock === 1 ? 'batería está' : 'baterías están' }} bajo el stock mínimo</span>
        <span class="block mt-1 text-base text-red-800">Considera reponerlas con el proveedor.</span>
        @endif
    </div>
    @endif
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'liberado'])"
        :valor="$counts['liberadas']"
        titulo="Por preparar"
        detalle="En manos de bodega, sin comenzar"
        color="blue" />

    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'preparando'])"
        :valor="$counts['preparando']"
        titulo="Preparando"
        detalle="Bodega las está escaneando"
        color="orange" />

    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'listo'])"
        :valor="$counts['listas']"
        titulo="Para entregar"
        detalle="Terminadas, esperando al cliente"
        color="green" />

    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'entregado'])"
        :valor="$counts['entregadas']"
        titulo="Entregadas"
        detalle="Ciclo completo"
        color="emerald" />

    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'cancelado'])"
        :valor="$counts['canceladas']"
        titulo="Canceladas"
        detalle="Anuladas antes de entregarse"
        color="red" />
</div>

<div class="mt-8 bg-slate-50 border border-slate-300 rounded-2xl p-6">
    <h3 class="text-xl font-bold text-slate-900">Accesos rápidos</h3>

    <div class="mt-4 flex flex-wrap gap-3">
        <x-wms.btn size="lg" href="{{ route('orders.create') }}">+ Crear orden</x-wms.btn>
        <x-wms.btn size="lg" variant="dark" href="{{ route('orders.index') }}">Ver todas las órdenes</x-wms.btn>
        <x-wms.btn size="lg" variant="secondary" href="{{ route('locations.index') }}">Mapa de bodega</x-wms.btn>
    </div>
</div>
@endsection
