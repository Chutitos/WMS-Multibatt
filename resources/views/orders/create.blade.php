@extends('layouts.wms')

@section('content')
<x-wms.page-header title="Crear orden" subtitle="Ingreso manual de una orden operativa." />

<div class="max-w-4xl mx-auto">
    <x-wms.errors />

    @if ($products->isEmpty())
    <div class="mb-6 rounded-xl border-2 border-amber-200 bg-amber-50 px-5 py-4 text-amber-800 text-base">
        Todavía no hay baterías activas en el catálogo.
        @if (auth()->user()->role->name === 'admin')
        <a href="{{ route('products.create') }}" class="font-semibold underline">Crea un producto</a> antes de armar una orden.
        @else
        Pídele al administrador que agregue las baterías antes de armar una orden.
        @endif
    </div>
    @endif

    <div class="bg-slate-50 rounded-2xl shadow-sm border border-slate-300 p-8">
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-5">
            @csrf

            <x-wms.field label="Cliente" name="cliente_nombre">
                <x-wms.input type="text" name="cliente_nombre" value="{{ old('cliente_nombre') }}" required />
            </x-wms.field>

            <x-wms.field label="RUT cliente" name="rut_cliente" :optional="true">
                <x-wms.input type="text" name="rut_cliente" value="{{ old('rut_cliente') }}" />
            </x-wms.field>

            <x-wms.field label="Tipo de entrega" name="tipo_entrega">
                <x-wms.select name="tipo_entrega" required>
                    <option value="">Seleccione</option>
                    <option value="retiro" {{ old('tipo_entrega') === 'retiro' ? 'selected' : '' }}>🏬 Retiro en tienda</option>
                    <option value="despacho" {{ old('tipo_entrega') === 'despacho' ? 'selected' : '' }}>🚚 Despacho a domicilio</option>
                </x-wms.select>
            </x-wms.field>

            <x-wms.field label="Observaciones" name="observaciones" :optional="true">
                <x-wms.textarea name="observaciones" rows="3">{{ old('observaciones') }}</x-wms.textarea>
            </x-wms.field>

            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Productos de la orden</h3>

                <div id="productos-container" class="space-y-4">
                    <div class="producto-item grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-white border border-slate-200 rounded-xl p-4">
                        <div class="md:col-span-7">
                            <label class="block text-base font-semibold text-slate-700 mb-2">Producto</label>
                            <select name="productos[0][product_id]"
                                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                required>
                                <option value="">Seleccione un producto</option>
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->sku }} — {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-base font-semibold text-slate-700 mb-2">Cantidad</label>
                            <input type="number" name="productos[0][cantidad]"
                                class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                                min="1" required>
                        </div>

                        <div class="md:col-span-2">
                            <button type="button"
                                class="eliminar-producto hidden w-full px-3 py-3 bg-red-50 text-red-600 rounded-xl text-base font-semibold hover:bg-red-100">
                                Quitar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button" id="agregar-producto"
                        class="w-full px-5 py-4 border-2 border-dashed border-slate-400 text-slate-600 rounded-2xl text-lg font-semibold hover:border-blue-500 hover:text-blue-600 transition">
                        + Agregar otro producto
                    </button>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <x-wms.btn variant="success" size="lg">Guardar orden</x-wms.btn>
                <x-wms.btn variant="secondary" href="{{ route('orders.index') }}">Volver</x-wms.btn>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productosDisponibles = @json($products->map(fn ($p) => ['id' => $p->id, 'label' => $p->sku . ' — ' . $p->name]));
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
                const selectProducto = item.querySelector('select');
                const cantidadInput = item.querySelector('input[type="number"]');

                selectProducto.name = `productos[${index}][product_id]`;
                cantidadInput.name = `productos[${index}][cantidad]`;
            });
        }

        function construirOpciones() {
            let opciones = '<option value="">Seleccione un producto</option>';

            productosDisponibles.forEach((producto) => {
                opciones += `<option value="${producto.id}">${producto.label}</option>`;
            });

            return opciones;
        }

        btnAgregar.addEventListener('click', function() {
            const total = container.querySelectorAll('.producto-item').length;

            const nuevo = document.createElement('div');
            nuevo.className = 'producto-item grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-white border border-slate-200 rounded-xl p-4';
            nuevo.innerHTML = `
                <div class="md:col-span-7">
                    <label class="block text-base font-semibold text-slate-700 mb-2">Producto</label>
                    <select name="productos[${total}][product_id]"
                        class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                        required>
                        ${construirOpciones()}
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-base font-semibold text-slate-700 mb-2">Cantidad</label>
                    <input type="number" name="productos[${total}][cantidad]"
                        class="w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                        min="1" required>
                </div>

                <div class="md:col-span-2">
                    <button type="button"
                        class="eliminar-producto w-full px-3 py-3 bg-red-50 text-red-600 rounded-xl text-base font-semibold hover:bg-red-100">
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
