@extends('layouts.wms')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-blue-900">Hola, {{ auth()->user()->name }}</h2>
    <p class="mt-2 text-lg text-slate-600">
        Así está la operación ahora. Toca una tarjeta para ver esas órdenes.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'creado'])"
        :valor="$counts['creadas']"
        titulo="Por liberar"
        detalle="Creadas, esperando liberación a bodega"
        color="slate" />

    <x-wms.stat-card
        :href="route('orders.index', ['estado' => 'liberado'])"
        :valor="$counts['liberadas']"
        titulo="Liberadas"
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
