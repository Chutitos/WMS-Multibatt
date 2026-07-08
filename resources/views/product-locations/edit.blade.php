@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Editar existencia" subtitle="Corrige el producto, la ubicación, el lote, la fecha o la cantidad." />

<x-wms.errors />

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('product-locations.update', $productLocation) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <x-wms.field label="Producto" name="product_id">
            <x-wms.select name="product_id" required>
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id', $productLocation->product_id) == $product->id ? 'selected' : '' }}>
                    {{ $product->sku }} — {{ $product->name }}{{ $product->active ? '' : ' (inactivo)' }}
                </option>
                @endforeach
            </x-wms.select>
        </x-wms.field>

        <x-wms.field label="Ubicación" name="warehouse_location_id">
            <x-wms.select name="warehouse_location_id" required>
                <option value="">Seleccione una ubicación</option>
                @foreach ($locations as $location)
                <option value="{{ $location->id }}" {{ old('warehouse_location_id', $productLocation->warehouse_location_id) == $location->id ? 'selected' : '' }}>
                    {{ $location->nombre }} ({{ $location->codigo }}){{ $location->activa ? '' : ' (inactiva)' }}
                </option>
                @endforeach
            </x-wms.select>
        </x-wms.field>

        <div class="grid grid-cols-2 gap-4">
            <x-wms.field label="Columna" name="columna" :optional="true" hint="Puesto físico dentro del rack.">
                <x-wms.input type="number" name="columna" min="1" value="{{ old('columna', $productLocation->columna) }}" />
            </x-wms.field>

            <x-wms.field label="Nivel" name="nivel" :optional="true">
                <x-wms.input type="number" name="nivel" min="1" value="{{ old('nivel', $productLocation->nivel) }}" />
            </x-wms.field>
        </div>

        <x-wms.field label="Lote" name="lote" :optional="true">
            <x-wms.input type="text" name="lote" value="{{ old('lote', $productLocation->lote) }}" />
        </x-wms.field>

        <x-wms.field label="Fecha de ingreso" name="fecha_ingreso">
            <x-wms.input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $productLocation->fecha_ingreso->format('Y-m-d')) }}" required />
        </x-wms.field>

        <x-wms.field label="Cantidad" name="cantidad" hint="Puedes dejarla en 0 si ya no queda existencia, sin necesidad de eliminar el registro.">
            <x-wms.input type="number" name="cantidad" min="0" value="{{ old('cantidad', $productLocation->cantidad) }}" required />
        </x-wms.field>

        <div class="pt-4 flex items-center gap-3">
            <x-wms.btn variant="success">Guardar cambios</x-wms.btn>
            <x-wms.btn variant="secondary" href="{{ route('product-locations.index') }}">Volver</x-wms.btn>
        </div>
    </form>
</div>
@endsection
