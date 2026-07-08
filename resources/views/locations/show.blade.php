@extends('layouts.wms')

@php
$rol = auth()->user()->role->name;
$esAdmin = $rol === 'admin';
$puedeAsignar = in_array($rol, ['admin', 'bodeguero'], true);
@endphp

@section('content')
<x-wms.page-header
    :title="$warehouseLocation->nombre"
    :subtitle="'Código ' . $warehouseLocation->codigo . ' — ' . $warehouseLocation->columnas . ' columna(s) × ' . $warehouseLocation->niveles . ' nivel(es). Cada puesto guarda un pallet de un solo tipo de batería.'">
    <x-slot:actions>
        @unless ($warehouseLocation->activa)
        <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-base font-semibold text-red-800">Inactiva</span>
        @endunless
        <x-wms.btn variant="secondary" href="{{ route('locations.index', ['destacar' => $warehouseLocation->id]) }}">Ver en el mapa</x-wms.btn>
    </x-slot:actions>
</x-wms.page-header>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 overflow-x-auto">
    <div class="min-w-fit space-y-3">
        @for ($nivel = $warehouseLocation->niveles; $nivel >= 1; $nivel--)
        <div class="flex items-stretch gap-3">
            <div class="w-20 flex-shrink-0 flex items-center justify-center bg-slate-100 rounded-xl text-base font-bold text-slate-600">
                Nivel {{ $nivel }}
            </div>

            <div class="flex-1 grid gap-3" style="grid-template-columns: repeat({{ $warehouseLocation->columnas }}, minmax(150px, 1fr));">
                @for ($columna = 1; $columna <= $warehouseLocation->columnas; $columna++)
                @php $pallet = $grilla[$nivel][$columna] ?? null; @endphp

                @if ($pallet)
                <div class="rounded-xl border-2 border-blue-300 bg-blue-50 p-3">
                    <div class="text-base font-bold text-slate-900 leading-tight">{{ $pallet->product->name }}</div>
                    <div class="mt-1 text-sm text-slate-600">
                        {{ $pallet->cantidad }} unidades
                        @if ($pallet->lote)
                        <span class="block">Lote {{ $pallet->lote }}</span>
                        @endif
                        <span class="block">Ingreso {{ $pallet->fecha_ingreso->format('d-m-Y') }}</span>
                    </div>
                    @if ($puedeAsignar)
                    <a href="{{ route('product-locations.edit', $pallet) }}"
                        class="mt-2 inline-block text-sm font-semibold text-blue-700 underline">Editar</a>
                    @endif
                </div>
                @else
                <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-3 flex flex-col items-center justify-center text-center min-h-[100px]">
                    <span class="text-sm text-slate-400">Col {{ $columna }} · Niv {{ $nivel }}</span>
                    @if ($puedeAsignar && $warehouseLocation->activa)
                    <a href="{{ route('product-locations.create', ['ubicacion' => $warehouseLocation->id, 'columna' => $columna, 'nivel' => $nivel]) }}"
                        class="mt-2 inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                        + Asignar
                    </a>
                    @endif
                </div>
                @endif
                @endfor
            </div>
        </div>
        @endfor
    </div>
</div>

@if ($sinPuesto->isNotEmpty())
<div class="mt-6 bg-amber-50 border-2 border-amber-200 rounded-2xl p-6">
    <h3 class="text-xl font-bold text-amber-900">Existencias sin puesto asignado</h3>
    <p class="mt-1 text-base text-slate-600">
        Estas existencias están registradas en este estante pero sin columna/nivel. Edítalas para ubicarlas en un puesto.
    </p>

    <div class="mt-4 space-y-2">
        @foreach ($sinPuesto as $pallet)
        <div class="flex items-center justify-between gap-4 bg-white border border-amber-200 rounded-xl px-4 py-3 {{ $pallet->cantidad === 0 ? 'opacity-50' : '' }}">
            <div class="text-base text-slate-900">
                <strong>{{ $pallet->product->name }}</strong> — {{ $pallet->cantidad }} unidades
                @if ($pallet->cantidad === 0)
                <span class="text-sm text-slate-500">(agotado)</span>
                @endif
                @if ($pallet->lote)
                · Lote {{ $pallet->lote }}
                @endif
            </div>
            @if ($puedeAsignar)
            <x-wms.btn size="sm" href="{{ route('product-locations.edit', $pallet) }}">Ubicar en un puesto</x-wms.btn>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@if ($esAdmin)
<div class="mt-6 max-w-xl bg-slate-50 border border-slate-300 rounded-2xl p-6">
    <h3 class="text-xl font-bold text-slate-900 mb-1">Dimensiones del rack</h3>
    <p class="text-base text-slate-600 mb-4">
        Cambia cuántas columnas y niveles tiene este rack. No podrás achicarlo si hay pallets en puestos que quedarían fuera.
    </p>

    <form method="POST" action="{{ route('locations.update', $warehouseLocation) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <x-wms.field label="Nombre" name="nombre">
            <x-wms.input type="text" name="nombre" value="{{ old('nombre', $warehouseLocation->nombre) }}" required />
        </x-wms.field>

        <div class="grid grid-cols-2 gap-4">
            <x-wms.field label="Columnas" name="columnas">
                <x-wms.input type="number" name="columnas" min="1" max="20" value="{{ old('columnas', $warehouseLocation->columnas) }}" required />
            </x-wms.field>

            <x-wms.field label="Niveles" name="niveles">
                <x-wms.input type="number" name="niveles" min="1" max="10" value="{{ old('niveles', $warehouseLocation->niveles) }}" required />
            </x-wms.field>
        </div>

        <x-wms.btn variant="success">Guardar dimensiones</x-wms.btn>
    </form>
</div>
@endif
@endsection
