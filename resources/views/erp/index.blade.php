@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Integración ERP (Defontana)"
    subtitle="Estado de la conexión con el ERP y documentos sincronizados. La conexión real se activará cuando existan credenciales de API." />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-2xl border-4 {{ $configurado ? 'border-blue-200 bg-blue-50' : 'border-slate-300 bg-slate-50' }} p-6">
        <h3 class="text-xl font-bold text-slate-900">Estado de la conexión</h3>

        <div class="mt-3 flex items-center gap-3">
            <span class="w-4 h-4 rounded-full {{ $conexion['ok'] ? 'bg-green-500' : ($configurado ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
            <span class="text-lg font-semibold text-slate-800">
                {{ $conexion['ok'] ? 'Conectado' : ($configurado ? 'Configurado, sin conexión' : 'No configurado') }}
            </span>
        </div>

        <p class="mt-3 text-base text-slate-600">{{ $conexion['mensaje'] }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h3 class="text-xl font-bold text-slate-900">Qué habilitará esta integración</h3>

        <ul class="mt-3 space-y-2 text-base text-slate-700 list-disc pl-5">
            <li><strong>Recepción de mercadería</strong>: las compras registradas en Defontana crearán el ingreso físico a los racks.</li>
            <li><strong>Órdenes desde ventas</strong>: los documentos de venta del ERP generarán órdenes de bodega automáticamente.</li>
            <li><strong>Stock contable</strong>: el stock oficial lo seguirá manejando Defontana; el WMS aporta la ubicación física y el FIFO.</li>
        </ul>

        <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-600">
            <p class="font-semibold text-slate-700 mb-1">Para activarla se necesita (pedir a Defontana):</p>
            <p>URL de la API, clave de acceso (API key) e identificador de empresa. Se cargan en el archivo <code class="font-mono">.env</code> del servidor como <code class="font-mono">DEFONTANA_BASE_URL</code>, <code class="font-mono">DEFONTANA_API_KEY</code> y <code class="font-mono">DEFONTANA_COMPANY_ID</code>.</p>
        </div>
    </div>
</div>

<div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h3 class="text-xl font-bold text-slate-900">Documentos sincronizados desde el ERP</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-base">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Documento</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Cliente</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Fecha</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Sincronización</th>
                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Último error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($documentos as $doc)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-900 capitalize">{{ $doc->tipo_documento }}</span>
                        <span class="text-slate-500">#{{ $doc->numero_documento }}</span>
                    </td>
                    <td class="px-6 py-4 text-slate-900">{{ $doc->cliente_nombre }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $doc->fecha_documento?->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">
                        @php
                        $badge = match ($doc->estado_sync) {
                            'imported' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $badge }} capitalize">
                            {{ $doc->estado_sync }}
                        </span>
                        @if ($doc->attempts > 1)
                        <span class="block text-xs text-slate-500 mt-1">{{ $doc->attempts }} intentos</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-red-600">{{ $doc->last_error ?: '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-4xl">🔌</div>
                        <p class="mt-3 text-xl font-bold text-slate-700">Todavía no hay documentos del ERP</p>
                        <p class="mt-1 text-base text-slate-500">
                            Cuando la conexión con Defontana esté activa, aquí aparecerán las ventas y compras sincronizadas.
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $documentos->links() }}
</div>
@endsection
