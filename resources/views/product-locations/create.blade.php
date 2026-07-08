@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Asignar existencia a una ubicación" subtitle="Registra cuánto de un producto quedó guardado en qué ubicación de la bodega." />

<x-wms.errors />

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('product-locations.store') }}" class="space-y-5">
        @csrf

        <x-wms.field label="Producto" name="product_id">
            <x-wms.select name="product_id" required>
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                    {{ $product->sku }} — {{ $product->name }}
                </option>
                @endforeach
            </x-wms.select>
        </x-wms.field>

        <x-wms.field label="Ubicación" name="warehouse_location_id">
            <x-wms.select name="warehouse_location_id" required>
                <option value="">Seleccione una ubicación</option>
                @foreach ($locations as $location)
                <option value="{{ $location->id }}" {{ old('warehouse_location_id', request('ubicacion')) == $location->id ? 'selected' : '' }}>
                    {{ $location->nombre }} ({{ $location->codigo }})
                </option>
                @endforeach
            </x-wms.select>
            @if ($locations->isEmpty())
            <p class="mt-2 text-base text-amber-700">
                No hay ubicaciones activas — <a href="{{ route('locations.index') }}" class="underline font-semibold">crea el mapa de bodega primero</a>.
            </p>
            @endif
        </x-wms.field>

        <div class="grid grid-cols-2 gap-4">
            <x-wms.field label="Columna" name="columna" :optional="true" hint="Puesto físico dentro del rack.">
                <x-wms.input type="number" name="columna" min="1" value="{{ old('columna', request('columna')) }}" />
            </x-wms.field>

            <x-wms.field label="Nivel" name="nivel" :optional="true">
                <x-wms.input type="number" name="nivel" min="1" value="{{ old('nivel', request('nivel')) }}" />
            </x-wms.field>
        </div>

        @if (request()->filled('columna'))
        {{-- Vino desde la grilla del estante: al guardar se vuelve allá. --}}
        <input type="hidden" name="volver_al_estante" value="1">
        @endif

        <x-wms.field label="Lote" name="lote" :optional="true">
            <x-wms.input type="text" name="lote" value="{{ old('lote') }}" />
        </x-wms.field>

        <x-wms.field label="Fecha de ingreso" name="fecha_ingreso">
            <x-wms.input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}" required />
        </x-wms.field>

        <x-wms.field label="Cantidad" name="cantidad">
            <x-wms.input type="number" name="cantidad" min="1" value="{{ old('cantidad') }}" required />
        </x-wms.field>

        <div class="pt-4 flex items-center gap-3">
            <x-wms.btn variant="success">Guardar</x-wms.btn>
            <x-wms.btn variant="secondary" href="{{ route('product-locations.index') }}">Volver</x-wms.btn>
        </div>
    </form>
</div>
@endsection
