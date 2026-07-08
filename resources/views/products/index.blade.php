@extends('layouts.wms')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Productos</h2>
        <p class="mt-2 text-sm text-slate-600">
            Catálogo de productos disponibles para armar órdenes.
        </p>
    </div>

    <a href="{{ route('products.create') }}"
        class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
        Crear producto
    </a>
</div>

<div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-300 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-300">
                <tr>
                    <th class="p-4 text-left font-semibold text-slate-700">SKU</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Nombre</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="p-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse ($products as $product)
                <tr>
                    <td class="p-4 text-slate-900 font-mono">{{ $product->sku }}</td>
                    <td class="p-4 text-slate-900">{{ $product->name }}</td>
                    <td class="p-4">
                        @if ($product->active)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Activo</span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">Inactivo</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <a href="{{ route('products.edit', $product) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                            Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-slate-500">
                        No hay productos registrados.
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
