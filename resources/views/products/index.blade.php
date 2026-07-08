@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Productos" subtitle="Catálogo de productos disponibles para armar órdenes.">
    <x-slot:actions>
        <x-wms.btn href="{{ route('products.create') }}">+ Crear producto</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">SKU</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Nombre</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Estado</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">
                @forelse ($products as $product)
                <tr class="hover:bg-slate-50 {{ $product->active ? '' : 'opacity-60' }}">
                    <td class="px-6 py-4 text-slate-900 font-mono">{{ $product->sku }}</td>
                    <td class="px-6 py-4 text-slate-900 font-semibold">{{ $product->name }}</td>
                    <td class="px-6 py-4">
                        <x-wms.badge-activo :activo="$product->active" />
                    </td>
                    <td class="px-6 py-4">
                        <x-wms.btn size="sm" href="{{ route('products.edit', $product) }}">Editar</x-wms.btn>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="text-4xl">🗃️</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">No hay productos registrados</p>
                        <p class="mt-1 text-base text-slate-500">Crea el primero con el botón "+ Crear producto".</p>
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
