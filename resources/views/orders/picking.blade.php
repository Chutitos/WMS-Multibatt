@extends('layouts.wms')

@section('content')
@php
// Completo = todos los items del catálogo ya escaneados (los de texto
// libre se completan solos al confirmar).
$todoCompleto = $order->items->every(
    fn ($item) => ! $item->product_id || $item->cantidad_confirmada >= $item->cantidad_solicitada
);
@endphp

<div class="mb-6">
    <div class="flex items-center gap-3 flex-wrap">
        <span class="w-10 h-10 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-xl">2</span>
        <h2 class="text-3xl font-bold text-slate-900">Escaneo — Orden #{{ $order->id }}</h2>
    </div>
    <p class="mt-3 text-lg text-slate-600">
        Cliente: <strong>{{ $order->cliente_nombre }}</strong>.
        Apunta la pistola al código de cada producto. El sistema te dice de qué estante tomarlo.
    </p>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-blue-50 border-4 border-blue-200 rounded-3xl p-6 mb-6">
        <label for="input-escaner" class="block text-xl font-bold text-blue-900 mb-3">
            📷 Escanea aquí
        </label>
        <input type="text" id="input-escaner" autofocus autocomplete="off"
            placeholder="Apunta la pistola y dispara"
            class="w-full rounded-2xl border-2 border-blue-300 text-2xl px-5 py-4 focus:border-blue-600 focus:ring-blue-600">
        <p id="mensaje-escaner" class="hidden mt-4 rounded-2xl px-5 py-4 text-xl font-semibold"></p>
    </div>

    <div class="space-y-3">
        @foreach ($order->items as $item)
        @php
        $completoItem = $item->cantidad_confirmada >= $item->cantidad_solicitada;
        @endphp
        <div class="fila-item bg-white border-2 {{ $completoItem ? 'border-green-300 bg-green-50' : 'border-slate-200' }} rounded-2xl p-5 flex flex-col md:flex-row md:items-center gap-4"
            data-item-id="{{ $item->id }}"
            data-product-id="{{ $item->product_id }}"
            data-completo="{{ $completoItem || ! $item->product_id ? '1' : '0' }}">

            <div class="flex-1">
                <div class="text-xl font-bold text-slate-900">{{ $item->producto_nombre }}</div>
                <div class="mt-1 text-lg text-slate-600 ubicacion-sugerida">
                    @if (! $item->product_id)
                    <span class="text-slate-400">Producto sin catálogo — se confirma solo al final</span>
                    @elseif (isset($sugerencias[$item->id]) && $sugerencias[$item->id])
                    📍 Tómalo de: <strong class="text-slate-900">{{ $sugerencias[$item->id]->warehouseLocation->nombre }}</strong>
                    <span class="font-mono text-base text-slate-500">({{ $sugerencias[$item->id]->warehouseLocation->codigo }})</span>
                    @if ($sugerencias[$item->id]->puesto())
                    <span class="text-slate-700">— {{ $sugerencias[$item->id]->puesto() }}</span>
                    @endif
                    <a href="{{ route('locations.index', ['destacar' => $sugerencias[$item->id]->warehouse_location_id]) }}"
                        class="ml-2 text-blue-600 underline text-base font-semibold whitespace-nowrap">Ver en el mapa</a>
                    @elseif ($completoItem)
                    <span class="text-green-700">Ya está completo</span>
                    @else
                    <span class="text-red-600 font-semibold">⚠ Sin existencia registrada en ningún estante</span>
                    @endif
                </div>
            </div>

            <div class="text-center md:w-40">
                <div class="text-3xl font-bold">
                    <span class="cantidad-progreso {{ $completoItem ? 'text-green-600' : 'text-slate-900' }}">{{ $item->cantidad_confirmada }}</span><span class="text-slate-400"> / </span><span class="cantidad-total text-slate-700">{{ $item->cantidad_solicitada }}</span>
                </div>
                <div class="marca-completo text-lg font-bold text-green-600 {{ $completoItem ? '' : 'invisible' }}">✅ Listo</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 flex flex-col md:flex-row items-stretch md:items-center gap-4">
        <form id="form-confirmar" method="POST" action="{{ route('orders.confirmar', $order) }}"
            class="flex-1 {{ $todoCompleto ? '' : 'hidden' }}">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="w-full px-6 py-5 bg-green-600 text-white text-xl font-bold rounded-2xl hover:bg-green-700 shadow-sm">
                ✅ Confirmar que está lista
            </button>
        </form>

        <p id="aviso-incompleto" class="flex-1 text-center text-lg text-slate-500 bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 {{ $todoCompleto ? 'hidden' : '' }}">
            Cuando termines de escanear todo, aquí aparecerá el botón para confirmar.
        </p>

        <a href="{{ route('orders.show', $order) }}"
            class="text-center px-6 py-4 bg-slate-100 border border-slate-300 text-slate-700 text-lg font-semibold rounded-2xl hover:bg-slate-200">
            Ver detalle
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('input-escaner');
        const mensaje = document.getElementById('mensaje-escaner');
        const formConfirmar = document.getElementById('form-confirmar');
        const avisoIncompleto = document.getElementById('aviso-incompleto');

        function mostrarMensaje(texto, ok) {
            mensaje.textContent = texto;
            mensaje.className = 'mt-4 rounded-2xl px-5 py-4 text-xl font-semibold ' +
                (ok ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
        }

        function revisarSiTodoCompleto() {
            const filas = document.querySelectorAll('.fila-item');
            const todoListo = Array.from(filas).every(f => f.dataset.completo === '1');

            formConfirmar.classList.toggle('hidden', !todoListo);
            avisoIncompleto.classList.toggle('hidden', todoListo);
        }

        input.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            const codigo = input.value.trim();
            input.value = '';
            if (!codigo) return;

            window.axios.post('{{ route('orders.picking.escanear', $order) }}', { codigo: codigo })
                .then(function(response) {
                    const data = response.data;
                    mostrarMensaje(data.message, true);

                    const fila = document.querySelector(`.fila-item[data-item-id="${data.item_id}"]`);
                    if (fila) {
                        fila.querySelector('.cantidad-progreso').textContent = data.cantidad_confirmada;

                        // El estante sugerido puede cambiar tras cada escaneo
                        // (FIFO: si el lote se agotó, la siguiente unidad está
                        // en otro estante). Se actualiza sin recargar.
                        const linea = fila.querySelector('.ubicacion-sugerida');
                        if (linea) {
                            if (data.completo) {
                                linea.innerHTML = '<span class="text-green-700">Ya está completo</span>';
                            } else if (data.siguiente_ubicacion) {
                                const u = data.siguiente_ubicacion;
                                const puesto = u.puesto ? ` <span class="text-slate-700">— ${u.puesto}</span>` : '';
                                linea.innerHTML = `📍 Tómalo de: <strong class="text-slate-900">${u.nombre}</strong>
                                    <span class="font-mono text-base text-slate-500">(${u.codigo})</span>${puesto}
                                    <a href="{{ route('locations.index') }}?destacar=${u.id}"
                                        class="ml-2 text-blue-600 underline text-base font-semibold whitespace-nowrap">Ver en el mapa</a>`;
                            } else {
                                linea.innerHTML = '<span class="text-red-600 font-semibold">⚠ Sin existencia registrada en ningún estante</span>';
                            }
                        }

                        if (data.completo) {
                            fila.dataset.completo = '1';
                            fila.classList.remove('border-slate-200');
                            fila.classList.add('border-green-300', 'bg-green-50');
                            fila.querySelector('.cantidad-progreso').classList.remove('text-slate-900');
                            fila.querySelector('.cantidad-progreso').classList.add('text-green-600');
                            fila.querySelector('.marca-completo').classList.remove('invisible');
                            revisarSiTodoCompleto();
                        }
                    }
                })
                .catch(function(error) {
                    mostrarMensaje(error.response?.data?.message || 'Error al escanear.', false);
                })
                .finally(function() {
                    input.focus();
                });
        });

        input.focus();
    });
</script>
@endsection
