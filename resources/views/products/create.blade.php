@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Crear producto" subtitle="Agrega un producto al catálogo." />

<x-wms.errors />

<div class="max-w-2xl mx-auto bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-6">
    <form method="POST" action="{{ route('products.store') }}" class="space-y-5">
        @csrf

        <x-wms.field label="SKU" name="sku">
            <x-wms.input type="text" name="sku" value="{{ old('sku') }}" required />
        </x-wms.field>

        <x-wms.field label="Código de barras" name="barcode" :optional="true" hint="El que lee la pistola escaneadora.">
            <x-wms.input type="text" name="barcode" value="{{ old('barcode') }}" />
        </x-wms.field>

        <x-wms.field label="Nombre" name="name">
            <x-wms.input type="text" name="name" value="{{ old('name') }}" required />
        </x-wms.field>

        <div class="border-t border-slate-200 pt-5">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Ficha técnica de la batería</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wms.field label="Marca" name="marca" :optional="true">
                    <x-wms.input type="text" name="marca" value="{{ old('marca') }}" placeholder="Bosch, Yuasa..." />
                </x-wms.field>

                <x-wms.field label="Tipo" name="tipo" :optional="true">
                    <x-wms.select name="tipo">
                        <option value="">Sin clasificar</option>
                        @foreach (\App\Models\Product::TIPOS as $valor => $etiqueta)
                        <option value="{{ $valor }}" {{ old('tipo') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </x-wms.select>
                </x-wms.field>

                <x-wms.field label="Voltaje" name="voltaje" :optional="true">
                    <x-wms.input type="text" name="voltaje" value="{{ old('voltaje') }}" placeholder="12V" />
                </x-wms.field>

                <x-wms.field label="Capacidad (Ah)" name="capacidad_ah" :optional="true">
                    <x-wms.input type="number" name="capacidad_ah" min="1" value="{{ old('capacidad_ah') }}" placeholder="75" />
                </x-wms.field>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-5">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Reglas de bodega</h3>

            <x-wms.field label="Stock mínimo físico" name="stock_minimo" hint="0 = sin alerta. Si la existencia baja de aquí, se avisa en el inicio.">
                <x-wms.input type="number" name="stock_minimo" min="0" value="{{ old('stock_minimo', 0) }}" required />
            </x-wms.field>
        </div>

        <x-wms.field label="Descripción" name="description" :optional="true">
            <x-wms.textarea name="description" rows="3">{{ old('description') }}</x-wms.textarea>
        </x-wms.field>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="active" class="text-base font-semibold text-slate-700 cursor-pointer">Activo (visible al crear órdenes)</label>
        </div>

        <div class="pt-4 flex items-center gap-3">
            <x-wms.btn variant="success">Guardar producto</x-wms.btn>
            <x-wms.btn variant="secondary" href="{{ route('products.index') }}">Volver</x-wms.btn>
        </div>
    </form>
</div>
@endsection
