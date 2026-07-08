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
