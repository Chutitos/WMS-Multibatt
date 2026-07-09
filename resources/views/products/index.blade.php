@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Productos" subtitle="Catálogo de baterías disponibles para armar órdenes.">
    <x-slot:actions>
        <x-wms.btn href="{{ route('products.create') }}">+ Crear producto</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<form method="GET" action="{{ route('products.index') }}"
    class="mb-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Buscar</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre, SKU, marca o código de barras"
            class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">Tipo</label>
        <select name="tipo" class="w-full rounded-xl border-2 border-slate-300 text-base px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
            <option value="">Todos</option>
            @foreach (\App\Models\Product::TIPOS as $valor => $etiqueta)
            <option value="{{ $valor }}" {{ request('tipo') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <x-wms.btn variant="dark">Buscar</x-wms.btn>

        @if (request()->filled('q') || request()->filled('tipo'))
        <x-wms.btn variant="secondary" href="{{ route('products.index') }}">Limpiar</x-wms.btn>
        @endif
    </div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">SKU</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Batería</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Tipo</th>
                    <th class="px-6 py-4 text-center font-semibold text-slate-700">Existencia física</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse ($products as $product)
                @php
                $existencia = (int) ($product->existencia_fisica ?? 0);
                $bajoStock = $product->stock_minimo > 0 && $existencia < $product->stock_minimo;
                @endphp
                <tr class="hover:bg-slate-50 {{ $product->active ? '' : 'opacity-60' }}">
                    <td class="px-6 py-4 text-slate-900 font-mono">{{ $product->sku }}</td>
                    <td class="px-6 py-4">
                        <div class="text-slate-900 font-semibold">{{ $product->name }}</div>
                        @if ($product->fichaCorta())
                        <div class="text-sm text-slate-500">{{ $product->fichaCorta() }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $product->tipoLabel() ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold {{ $bajoStock ? 'text-red-600' : 'text-slate-900' }}">{{ $existencia }}</span>
                        @if ($bajoStock)
                        <span class="block text-xs font-semibold text-red-600">Bajo el mínimo ({{ $product->stock_minimo }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <x-wms.badge-activo :activo="$product->active" />
                    </td>
                    <td class="px-6 py-4">
                        <x-wms.btn size="sm" href="{{ route('products.edit', $product) }}">Editar</x-wms.btn>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-4xl">🔋</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">No hay baterías con esta búsqueda</p>
                        <p class="mt-1 text-base text-slate-500">Prueba con otro texto o limpia los filtros.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
