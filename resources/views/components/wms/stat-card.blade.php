@props(['href' => null, 'valor', 'titulo', 'detalle' => null, 'color' => 'blue'])

@php
$colores = [
    'blue' => ['caja' => 'bg-blue-50 border-blue-200 hover:bg-blue-100 hover:border-blue-400', 'numero' => 'text-blue-700', 'titulo' => 'text-blue-900'],
    'orange' => ['caja' => 'bg-orange-50 border-orange-200 hover:bg-orange-100 hover:border-orange-400', 'numero' => 'text-orange-600', 'titulo' => 'text-orange-900'],
    'green' => ['caja' => 'bg-green-50 border-green-200 hover:bg-green-100 hover:border-green-400', 'numero' => 'text-green-600', 'titulo' => 'text-green-900'],
    'emerald' => ['caja' => 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100 hover:border-emerald-400', 'numero' => 'text-emerald-600', 'titulo' => 'text-emerald-900'],
    'red' => ['caja' => 'bg-red-50 border-red-200 hover:bg-red-100 hover:border-red-400', 'numero' => 'text-red-500', 'titulo' => 'text-red-900'],
    'slate' => ['caja' => 'bg-slate-50 border-slate-200 hover:bg-slate-100 hover:border-slate-400', 'numero' => 'text-slate-700', 'titulo' => 'text-slate-900'],
];

$c = $colores[$color] ?? $colores['blue'];
$clases = 'block border-4 rounded-3xl p-6 text-center transition ' . $c['caja'];
@endphp

@if ($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
@else
<div {{ $attributes->merge(['class' => $clases]) }}>
@endif
    <div class="text-5xl font-bold {{ $c['numero'] }}">{{ $valor }}</div>
    <div class="mt-2 text-xl font-bold {{ $c['titulo'] }}">{{ $titulo }}</div>
    @if ($detalle)
    <div class="mt-1 text-base text-slate-600">{{ $detalle }}</div>
    @endif
@if ($href)
</a>
@else
</div>
@endif
