@extends('layouts.wms')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-blue-900">Hola, {{ auth()->user()->name }}</h2>
    <p class="mt-2 text-lg text-slate-600">
        Toca una tarjeta para ir directo a esa lista de órdenes.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('bodega.index') }}"
        class="block bg-blue-50 border-4 border-blue-200 rounded-3xl p-8 text-center hover:bg-blue-100 hover:border-blue-400 transition">
        <div class="text-6xl font-bold text-blue-700">{{ $counts['liberadas'] }}</div>
        <div class="mt-3 text-2xl font-bold text-blue-900">Por preparar</div>
        <div class="mt-2 text-base text-slate-600">Órdenes nuevas esperando que alguien las tome</div>
    </a>

    <a href="{{ route('bodega.preparando') }}"
        class="block bg-orange-50 border-4 border-orange-200 rounded-3xl p-8 text-center hover:bg-orange-100 hover:border-orange-400 transition">
        <div class="text-6xl font-bold text-orange-600">{{ $counts['preparando'] }}</div>
        <div class="mt-3 text-2xl font-bold text-orange-900">Preparando</div>
        <div class="mt-2 text-base text-slate-600">Órdenes a medio escanear con la pistola</div>
    </a>

    <a href="{{ route('bodega.listo') }}"
        class="block bg-green-50 border-4 border-green-200 rounded-3xl p-8 text-center hover:bg-green-100 hover:border-green-400 transition">
        <div class="text-6xl font-bold text-green-600">{{ $counts['listas'] }}</div>
        <div class="mt-3 text-2xl font-bold text-green-900">Para entregar</div>
        <div class="mt-2 text-base text-slate-600">Órdenes terminadas esperando al cliente</div>
    </a>
</div>

<div class="mt-8 bg-slate-50 border border-slate-300 rounded-2xl p-6">
    <h3 class="text-xl font-bold text-slate-900">¿Cómo se trabaja una orden?</h3>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-lg">
        <div class="flex items-start gap-3">
            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xl">1</span>
            <p class="text-slate-700">En <strong>Por preparar</strong>, toca <strong>"Comenzar a preparar"</strong> en la orden que vas a trabajar.</p>
        </div>
        <div class="flex items-start gap-3">
            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xl">2</span>
            <p class="text-slate-700">En <strong>Preparando</strong>, escanea cada producto con la pistola hasta completar la orden.</p>
        </div>
        <div class="flex items-start gap-3">
            <span class="flex-shrink-0 w-10 h-10 rounded-full bg-green-600 text-white font-bold flex items-center justify-center text-xl">3</span>
            <p class="text-slate-700">En <strong>Para entregar</strong>, registra quién retira o el despacho, y listo.</p>
        </div>
    </div>
</div>
@endsection
