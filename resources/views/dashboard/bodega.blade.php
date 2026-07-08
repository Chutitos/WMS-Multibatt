@extends('layouts.wms')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-blue-900">Inicio - Bodega</h2>
    <p class="mt-2 text-sm text-slate-600">
        Resumen de órdenes disponibles para tu trabajo en bodega.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">Liberadas disponibles</div>
        <div class="mt-3 text-3xl font-bold text-blue-600">{{ $counts['liberadas'] }}</div>
    </div>

    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">En preparación</div>
        <div class="mt-3 text-3xl font-bold text-orange-500">{{ $counts['preparando'] }}</div>
    </div>

    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">Listas para entregar</div>
        <div class="mt-3 text-3xl font-bold text-green-600">{{ $counts['listas'] }}</div>
    </div>
</div>

<div class="mt-6 bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-5">
    <h3 class="text-lg font-semibold text-blue-900">Accesos rápidos</h3>

    <div class="mt-4 flex flex-wrap gap-3">
        <a href="{{ route('bodega.index') }}"
            class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
            Ver liberadas
        </a>

        <a href="{{ route('bodega.preparando') }}"
            class="inline-flex items-center px-5 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900">
            Ver en preparación
        </a>

        <a href="{{ route('bodega.listo') }}"
            class="inline-flex items-center px-5 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900">
            Ver listas
        </a>
    </div>
</div>
@endsection
