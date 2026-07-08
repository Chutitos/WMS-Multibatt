@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-slate-900">Picking — Orden #{{ $order->id }}</h2>
    <p class="mt-2 text-sm text-slate-600">
        Escanea el código de cada producto para confirmarlo. El sistema te indica de qué ubicación tomarlo (FIFO: la existencia más antigua primero).
    </p>
</div>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Escanear código</label>
        <input type="text" id="input-escaner" autofocus
            placeholder="Apunta la pistola aquí y escanea"
            class="w-full rounded-xl border-slate-300 text-lg focus:border-blue-500 focus:ring-blue-500">
        <p id="mensaje-escaner" class="mt-2 text-sm"></p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-slate-700">Producto</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-700">Ubicación sugerida (FIFO)</th>
                        <th class="px-6 py-4 text-left font-semibold text-slate-700">Progreso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($order->items as $item)
                    <tr data-item-id="{{ $item->id }}" data-product-id="{{ $item->product_id }}">
                        <td class="px-6 py-4 text-slate-900">{{ $item->producto_nombre }}</td>
                        <td class="px-6 py-4 text-slate-700">
                            @if (! $item->product_id)
                            <span class="text-slate-400">Sin catálogo</span>
                            @elseif (isset($sugerencias[$item->id]) && $sugerencias[$item->id])
                            {{ $sugerencias[$item->id]->warehouseLocation->nombre }}
                            <span class="font-mono text-xs text-slate-500">({{ $sugerencias[$item->id]->warehouseLocation->codigo }})</span>
                            @elseif ($item->cantidad_confirmada >= $item->cantidad_solicitada)
                            <span class="text-green-600">—</span>
                            @else
                            <span class="text-red-600">Sin existencia registrada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="cantidad-progreso {{ $item->cantidad_confirmada >= $item->cantidad_solicitada ? 'text-green-600' : 'text-slate-900' }} font-semibold">
                                {{ $item->cantidad_confirmada }}
                            </span>
                            / <span class="cantidad-total">{{ $item->cantidad_solicitada }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('orders.show', $order) }}"
            class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
            Volver al detalle
        </a>

        <form method="POST" action="{{ route('orders.confirmar', $order) }}">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                Confirmar productos y pasar a listo
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('input-escaner');
        const mensaje = document.getElementById('mensaje-escaner');

        input.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const codigo = input.value.trim();
            input.value = '';
            if (!codigo) return;

            window.axios.post('{{ route('orders.picking.escanear', $order) }}', { codigo: codigo })
                .then(function(response) {
                    const data = response.data;
                    mensaje.textContent = data.message;
                    mensaje.className = 'mt-2 text-sm text-green-700';

                    const fila = document.querySelector(`tr[data-item-id="${data.item_id}"]`);
                    if (fila) {
                        const progreso = fila.querySelector('.cantidad-progreso');
                        progreso.textContent = data.cantidad_confirmada;
                        if (data.completo) {
                            progreso.classList.remove('text-slate-900');
                            progreso.classList.add('text-green-600');
                        }
                    }
                })
                .catch(function(error) {
                    mensaje.textContent = error.response?.data?.message || 'Error al escanear.';
                    mensaje.className = 'mt-2 text-sm text-red-700';
                })
                .finally(function() {
                    input.focus();
                });
        });

        input.focus();
    });
</script>
@endsection
