@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Asignar existencia a una ubicación</h2>
    <p class="mt-2 text-sm text-slate-600">
        Registra cuánto de un producto quedó guardado en qué ubicación de la bodega.
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
    <form method="POST" action="{{ route('product-locations.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Producto</label>
            <select name="product_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                    {{ $product->sku }} — {{ $product->name }}
                </option>
                @endforeach
            </select>
            @error('product_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Ubicación</label>
            <select name="warehouse_location_id" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Seleccione una ubicación</option>
                @foreach ($locations as $location)
                <option value="{{ $location->id }}" {{ old('warehouse_location_id') == $location->id ? 'selected' : '' }}>
                    {{ $location->nombre }} ({{ $location->codigo }})
                </option>
                @endforeach
            </select>
            @error('warehouse_location_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if ($locations->isEmpty())
            <p class="mt-2 text-sm text-amber-700">
                No hay ubicaciones activas — <a href="{{ route('locations.index') }}" class="underline font-semibold">crea el mapa de bodega primero</a>.
            </p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Lote (opcional)</label>
            <input type="text" name="lote" value="{{ old('lote') }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            @error('lote')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Fecha de ingreso</label>
            <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
            @error('fecha_ingreso')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Cantidad</label>
            <input type="number" name="cantidad" min="1" value="{{ old('cantidad') }}"
                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500" required>
            @error('cantidad')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                Guardar
            </button>

            <a href="{{ route('product-locations.index') }}"
                class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
                Volver
            </a>
        </div>
    </form>
</div>
@endsection
