@extends('layouts.wms')

@php
$esAdmin = auth()->user()->role->name === 'admin';
@endphp

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Mapa de bodega</h2>
        <p class="mt-2 text-sm text-slate-600">
            @if ($esAdmin)
            Arrastra las ubicaciones para armar el mapa. Haz clic (sin arrastrar) para ver qué hay guardado en una ubicación.
            @else
            Vista de solo lectura. Haz clic en una ubicación para ver qué hay guardado ahí.
            @endif
        </p>
    </div>

    @if ($esAdmin)
    <button type="button" id="btn-agregar-ubicacion"
        class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
        + Agregar ubicación
    </button>
    @endif
</div>

<div id="mapa" class="relative bg-slate-100 border-2 border-dashed border-slate-300 rounded-2xl overflow-auto" style="width: 100%; height: 640px;">
    @foreach ($locations as $location)
    @php
    $contenido = $location->productLocations->map(fn ($pl) => [
        'producto' => $pl->product->name,
        'lote' => $pl->lote,
        'cantidad' => $pl->cantidad,
    ]);
    $ocupada = $contenido->sum('cantidad') > 0;
    @endphp
    <div class="ubicacion-box absolute select-none rounded-lg border-2 shadow-sm flex flex-col items-center justify-center p-2 text-center {{ $esAdmin ? 'cursor-move' : 'cursor-pointer' }} {{ $location->activa ? 'bg-blue-50 ' . ($ocupada ? 'border-blue-400' : 'border-slate-300') : 'bg-slate-200 border-slate-300 opacity-60' }}"
        data-id="{{ $location->id }}"
        data-nombre="{{ $location->nombre }}"
        data-codigo="{{ $location->codigo }}"
        data-activa="{{ $location->activa ? '1' : '0' }}"
        data-contenido='@json($contenido)'
        style="left: {{ $location->pos_x }}px; top: {{ $location->pos_y }}px; width: {{ $location->width }}px; height: {{ $location->height }}px;">
        <span class="font-semibold text-slate-900 text-sm">{{ $location->nombre }}</span>
        <span class="font-mono text-xs text-slate-500">{{ $location->codigo }}</span>
        @unless ($location->activa)
        <span class="text-xs text-red-600 font-semibold">(inactiva)</span>
        @endunless
        <span class="mt-1 h-2 w-2 rounded-full {{ $ocupada ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
    </div>
    @endforeach

    <div id="popover-ubicacion" class="hidden absolute z-20 w-72 bg-white border border-slate-300 rounded-xl shadow-lg">
        <div class="flex items-start justify-between px-4 py-3 border-b border-slate-200">
            <div>
                <p id="popover-nombre" class="font-semibold text-slate-900 text-sm"></p>
                <p id="popover-codigo" class="font-mono text-xs text-slate-500"></p>
            </div>
            <button type="button" id="popover-cerrar" class="text-slate-400 hover:text-slate-700 text-lg leading-none">&times;</button>
        </div>
        <ul id="popover-lista" class="max-h-56 overflow-y-auto px-4 py-2 divide-y divide-slate-100"></ul>
        @if ($esAdmin)
        <div class="px-4 py-3 border-t border-slate-200">
            <button type="button" id="popover-toggle-activa" class="w-full px-3 py-2 rounded-lg text-sm font-semibold"></button>
        </div>
        @endif
    </div>
</div>

<p class="mt-4 text-sm text-slate-500">
    {{ $locations->count() }} ubicación(es) en el mapa. El punto azul indica que tiene productos guardados.
</p>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const esAdmin = @json($esAdmin);
        const mapa = document.getElementById('mapa');
        const btnAgregar = document.getElementById('btn-agregar-ubicacion');

        const popover = document.getElementById('popover-ubicacion');
        const popoverNombre = document.getElementById('popover-nombre');
        const popoverCodigo = document.getElementById('popover-codigo');
        const popoverLista = document.getElementById('popover-lista');
        const popoverCerrar = document.getElementById('popover-cerrar');
        const btnToggleActiva = document.getElementById('popover-toggle-activa');

        function ocultarPopover() {
            popover.classList.add('hidden');
            popover.dataset.openFor = '';
        }

        function mostrarPopover(box) {
            if (popover.dataset.openFor === box.dataset.id) {
                ocultarPopover();
                return;
            }

            const contenido = JSON.parse(box.dataset.contenido || '[]');

            popoverNombre.textContent = box.dataset.nombre;
            popoverCodigo.textContent = box.dataset.codigo;
            popoverLista.innerHTML = '';

            if (contenido.length === 0) {
                popoverLista.innerHTML = '<li class="py-3 text-sm text-slate-500">Sin productos guardados.</li>';
            } else {
                contenido.forEach(function(item) {
                    const li = document.createElement('li');
                    li.className = 'py-2 flex items-center justify-between gap-3';
                    li.innerHTML = `
                        <span class="text-sm text-slate-900">
                            ${item.producto}
                            ${item.lote ? `<span class="block text-xs text-slate-400">Lote: ${item.lote}</span>` : ''}
                        </span>
                        <span class="text-sm font-semibold text-slate-900 whitespace-nowrap">${item.cantidad} uds</span>
                    `;
                    popoverLista.appendChild(li);
                });
            }

            if (btnToggleActiva) {
                const activa = box.dataset.activa === '1';
                btnToggleActiva.textContent = activa ? 'Desactivar ubicación' : 'Activar ubicación';
                btnToggleActiva.className = 'w-full px-3 py-2 rounded-lg text-sm font-semibold ' +
                    (activa ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100');
            }

            const left = Math.min(parseInt(box.style.left, 10), mapa.clientWidth - 300);
            const top = parseInt(box.style.top, 10) + box.offsetHeight + 10;

            popover.style.left = Math.max(0, left) + 'px';
            popover.style.top = top + 'px';
            popover.classList.remove('hidden');
            popover.dataset.openFor = box.dataset.id;
        }

        popoverCerrar.addEventListener('click', ocultarPopover);

        if (btnToggleActiva) {
            btnToggleActiva.addEventListener('click', function() {
                const box = document.querySelector(`.ubicacion-box[data-id="${popover.dataset.openFor}"]`);
                if (!box) return;

                const activar = box.dataset.activa !== '1';
                const verbo = activar ? 'activar' : 'desactivar';
                if (!confirm(`¿Seguro que quieres ${verbo} la ubicación "${box.dataset.nombre}"?`)) return;

                window.axios.patch(`/ubicaciones/${box.dataset.id}`, { activa: activar })
                    .then(function() {
                        window.location.reload();
                    })
                    .catch(function(error) {
                        alert(error.response?.data?.message || 'No se pudo cambiar el estado de la ubicación.');
                    });
            });
        }

        document.addEventListener('click', function(e) {
            if (popover.classList.contains('hidden')) return;
            if (popover.contains(e.target) || e.target.closest('.ubicacion-box')) return;
            ocultarPopover();
        });

        function activarCaja(box) {
            if (!esAdmin) {
                box.addEventListener('click', function() {
                    mostrarPopover(box);
                });
                return;
            }

            let dragging = false;
            let moved = false;
            let startX = 0;
            let startY = 0;
            let offsetX = 0;
            let offsetY = 0;

            box.addEventListener('mousedown', function(e) {
                dragging = true;
                moved = false;
                startX = e.clientX;
                startY = e.clientY;
                offsetX = e.clientX - box.offsetLeft;
                offsetY = e.clientY - box.offsetTop;
                box.style.zIndex = 10;
            });

            document.addEventListener('mousemove', function(e) {
                if (!dragging) return;

                if (Math.abs(e.clientX - startX) > 3 || Math.abs(e.clientY - startY) > 3) {
                    moved = true;
                    ocultarPopover();
                }

                let newLeft = Math.max(0, e.clientX - offsetX);
                let newTop = Math.max(0, e.clientY - offsetY);

                box.style.left = newLeft + 'px';
                box.style.top = newTop + 'px';
            });

            document.addEventListener('mouseup', function() {
                if (!dragging) return;
                dragging = false;
                box.style.zIndex = '';

                if (moved) {
                    window.axios.patch(`/ubicaciones/${box.dataset.id}`, {
                        pos_x: parseInt(box.style.left, 10),
                        pos_y: parseInt(box.style.top, 10),
                    }).catch(function() {
                        alert('No se pudo guardar la posición de la ubicación.');
                    });
                } else {
                    mostrarPopover(box);
                }
            });
        }

        function crearCaja(location) {
            const div = document.createElement('div');
            div.className = 'ubicacion-box absolute cursor-move select-none rounded-lg border-2 border-slate-300 bg-blue-50 shadow-sm flex flex-col items-center justify-center p-2 text-center';
            div.dataset.id = location.id;
            div.dataset.nombre = location.nombre;
            div.dataset.codigo = location.codigo;
            div.dataset.activa = '1';
            div.dataset.contenido = '[]';
            div.style.left = location.pos_x + 'px';
            div.style.top = location.pos_y + 'px';
            div.style.width = location.width + 'px';
            div.style.height = location.height + 'px';
            div.innerHTML = `
                <span class="font-semibold text-slate-900 text-sm">${location.nombre}</span>
                <span class="font-mono text-xs text-slate-500">${location.codigo}</span>
                <span class="mt-1 h-2 w-2 rounded-full bg-slate-300"></span>
            `;

            mapa.appendChild(div);
            activarCaja(div);
        }

        document.querySelectorAll('.ubicacion-box').forEach(activarCaja);

        if (esAdmin && btnAgregar) {
            btnAgregar.addEventListener('click', function() {
                const nombre = prompt('Nombre de la ubicación (ej: Estante A1):');
                if (!nombre) return;

                const codigo = prompt('Código único (ej: A-01):');
                if (!codigo) return;

                window.axios.post('/ubicaciones', { nombre: nombre, codigo: codigo })
                    .then(function(response) {
                        crearCaja(response.data);
                    })
                    .catch(function(error) {
                        const mensaje = error.response?.data?.message || 'No se pudo crear la ubicación.';
                        alert(mensaje);
                    });
            });
        }
    });
</script>
@endsection
