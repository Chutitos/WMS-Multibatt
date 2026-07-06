@extends('layouts.wms')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-slate-900">Crear orden</h2>
    <p class="mt-2 text-sm text-slate-600">
        Ingreso manual de una orden operativa.
    </p>
</div>

<div class="flex justify-center">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-sm border border-slate-300 p-8">
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Cliente</label>
                <input type="text" name="cliente_nombre"
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">RUT cliente</label>
                <input type="text" name="rut_cliente"
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tipo de entrega</label>
                <select name="tipo_entrega"
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">Seleccione</option>
                    <option value="retiro">Retiro</option>
                    <option value="despacho">Despacho</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Observaciones</label>
                <textarea name="observaciones" rows="4"
                    class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="border-t border-slate-200 pt-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Productos</h3>
                </div>

                <div id="productos-container" class="space-y-4">
                    <div class="producto-item grid grid-cols-1 md:grid-cols-12 gap-4 items-end border border-slate-200 rounded-xl p-4">
                        <div class="md:col-span-7">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre producto</label>
                            <input type="text" name="productos[0][nombre]"
                                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                required>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cantidad</label>
                            <input type="number" name="productos[0][cantidad]"
                                class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                min="1" required>
                        </div>

                        <div class="md:col-span-2">
                            <button type="button"
                                class="eliminar-producto hidden w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                Quitar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    <button type="button" id="agregar-producto"
                        class="h-16 w-16 rounded-full border-2 border-slate-400 text-slate-500 flex items-center justify-center hover:border-blue-500 hover:text-blue-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                    Guardar orden
                </button>

                <a href="{{ route('orders.index') }}"
                    class="inline-flex items-center px-5 py-3 bg-slate-200 text-slate-800 rounded-xl font-semibold hover:bg-slate-300">
                    Volver
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('productos-container');
        const btnAgregar = document.getElementById('agregar-producto');

        function actualizarBotonesEliminar() {
            const items = container.querySelectorAll('.producto-item');
            const mostrarEliminar = items.length > 1;

            items.forEach((item) => {
                const btnEliminar = item.querySelector('.eliminar-producto');

                if (mostrarEliminar) {
                    btnEliminar.classList.remove('hidden');
                } else {
                    btnEliminar.classList.add('hidden');
                }
            });
        }

        function reindexarProductos() {
            const items = container.querySelectorAll('.producto-item');

            items.forEach((item, index) => {
                const nombreInput = item.querySelector('input[type="text"]');
                const cantidadInput = item.querySelector('input[type="number"]');

                nombreInput.name = `productos[${index}][nombre]`;
                cantidadInput.name = `productos[${index}][cantidad]`;
            });
        }

        btnAgregar.addEventListener('click', function() {
            const total = container.querySelectorAll('.producto-item').length;

            const nuevo = document.createElement('div');
            nuevo.className = 'producto-item grid grid-cols-1 md:grid-cols-12 gap-4 items-end border border-slate-200 rounded-xl p-4';
            nuevo.innerHTML = `
                <div class="md:col-span-7">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre producto</label>
                    <input type="text" name="productos[${total}][nombre]"
                        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Cantidad</label>
                    <input type="number" name="productos[${total}][cantidad]"
                        class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        min="1" required>
                </div>

                <div class="md:col-span-2">
                    <button type="button"
                        class="eliminar-producto w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                        Quitar
                    </button>
                </div>
            `;

            container.appendChild(nuevo);
            reindexarProductos();
            actualizarBotonesEliminar();
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('eliminar-producto')) {
                const items = container.querySelectorAll('.producto-item');

                if (items.length === 1) {
                    return;
                }

                e.target.closest('.producto-item').remove();
                reindexarProductos();
                actualizarBotonesEliminar();
            }
        });

        actualizarBotonesEliminar();
    });
</script>
@endsection