@extends('layouts.wms')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Historial de existencias</h2>
        <p class="mt-2 text-sm text-slate-600">
            Quién creó, editó o eliminó cada registro de existencia y qué cambió.
        </p>
    </div>

    <a href="{{ route('product-locations.index') }}"
        class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
        Volver a existencias
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Acción</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Usuario</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($eventos as $evento)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 text-slate-700 whitespace-nowrap">
                        {{ $evento->created_at->format('d-m-Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $badge = match ($evento->accion) {
                            'creada' => 'bg-green-100 text-green-800',
                            'editada' => 'bg-orange-100 text-orange-800',
                            'eliminada' => 'bg-red-100 text-red-800',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ ucfirst($evento->accion) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-900">
                        {{ $evento->user?->name ?? 'Usuario eliminado' }}
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $evento->detalle }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                        Todavía no hay movimientos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $eventos->links() }}
</div>
@endsection
