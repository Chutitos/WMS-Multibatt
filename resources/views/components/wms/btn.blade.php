@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'submit'])

@php
$variantes = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
    'success' => 'bg-green-600 text-white hover:bg-green-700',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
    'secondary' => 'bg-slate-200 text-slate-800 hover:bg-slate-300',
    'dark' => 'bg-slate-800 text-white hover:bg-slate-900',
];

$tamanos = [
    'sm' => 'px-4 py-2 text-sm rounded-lg font-semibold',
    'md' => 'px-5 py-3 text-base rounded-xl font-semibold',
    'lg' => 'px-6 py-4 text-lg rounded-2xl font-bold',
    'xl' => 'w-full px-6 py-5 text-xl rounded-2xl font-bold shadow-sm',
];

$clases = 'inline-flex items-center justify-center text-center transition '
    . ($variantes[$variant] ?? $variantes['primary']) . ' '
    . ($tamanos[$size] ?? $tamanos['md']);
@endphp

@if ($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $clases]) }}>{{ $slot }}</button>
@endif
