@extends('layouts.wms')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-blue-900">Inicio - Jefe de Bodega</h2>
    <p class="mt-2 text-sm text-slate-600">
        Gestión operativa de órdenes y liberación a bodega.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">Órdenes creadas</div>
        <div class="mt-3 text-3xl font-bold text-blue-600">0</div>
    </div>

    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">Liberadas a bodega</div>
        <div class="mt-3 text-3xl font-bold text-blue-600">0</div>
    </div>

    <div class="bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-6">
        <div class="text-sm text-slate-500">Listas para entregar</div>
        <div class="mt-3 text-3xl font-bold text-blue-600">0</div>
    </div>
</div>

<div class="mt-6 bg-slate-50 rounded-2xl shadow-md border border-slate-300 p-5">
    <h3 class="text-lg font-semibold text-blue-900">Accesos rápidos</h3>

    <div class="mt-4 flex flex-wrap gap-3">
        <a href="{{ route('orders.create') }}"
            class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
            Crear orden
        </a>

        <a href="{{ route('orders.index') }}"
            class="inline-flex items-center px-5 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900">
            Ver órdenes
        </a>
    </div>
</div>
@endsection