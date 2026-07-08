@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Editar producto</h2>
    <p class="mt-2 text-sm text-slate-600">
        Modifica los datos del producto.
    </p>
</div>

@if ($errors->any())
<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
    <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">SKU</label>
            <input
                type="text"
                name="sku"
                value="{{ old('sku', $product->sku) }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('sku')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Código de barras</label>
            <input
                type="text"
                name="barcode"
                value="{{ old('barcode', $product->barcode) }}"
                placeholder="Opcional — el que lee la pistola"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('barcode')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $product->name) }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required>
            @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
            <textarea name="description" rows="3"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
            @error('description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="active" class="text-sm font-semibold text-slate-700">Activo (visible al crear órdenes)</label>
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button
                type="submit"
                class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                Guardar cambios
            </button>

            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
                Volver
            </a>
        </div>
    </form>
</div>
@endsection
