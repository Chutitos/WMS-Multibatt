@props(['activo', 'onLabel' => 'Activo', 'offLabel' => 'Inactivo'])

@if ($activo)
<span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">{{ $onLabel }}</span>
@else
<span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-sm font-semibold text-slate-600">{{ $offLabel }}</span>
@endif
