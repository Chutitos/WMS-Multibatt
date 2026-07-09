<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\OrderItemPick;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración: catálogo de baterías, racks con pallets y
 * órdenes en cada estado del flujo, para que la demo no parta vacía.
 *
 * Uso:  php artisan db:seed --class=DemoSeeder
 * (requiere roles y usuarios ya creados: php artisan db:seed)
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::where('sku', 'BAT-S4-60')->exists()) {
            $this->command->warn('Los datos de demo ya existen. Nada que hacer.');

            return;
        }

        $admin = $this->usuario('admin');
        $jefe = $this->usuario('jefe_bodega');
        $bodeguero = $this->usuario('bodeguero');

        // ── Catálogo ────────────────────────────────────────────────
        $s4 = Product::create(['sku' => 'BAT-S4-60', 'barcode' => '7791234000601', 'name' => 'Batería Bosch S4 60Ah', 'marca' => 'Bosch', 'tipo' => 'auto', 'voltaje' => '12V', 'capacidad_ah' => 60, 'stock_minimo' => 5, 'active' => true]);
        $s5 = Product::create(['sku' => 'BAT-S5-75', 'barcode' => '7791234000752', 'name' => 'Batería Bosch S5 75Ah', 'marca' => 'Bosch', 'tipo' => 'camioneta', 'voltaje' => '12V', 'capacidad_ah' => 75, 'stock_minimo' => 4, 'active' => true]);
        $moto = Product::create(['sku' => 'BAT-YT7B', 'barcode' => '7791234000073', 'name' => 'Batería Yuasa YT7B Moto', 'marca' => 'Yuasa', 'tipo' => 'moto', 'voltaje' => '12V', 'capacidad_ah' => 7, 'stock_minimo' => 3, 'active' => true]);
        $camion = Product::create(['sku' => 'BAT-TR-180', 'barcode' => '7791234001803', 'name' => 'Batería Camión 180Ah', 'marca' => 'Fulgor', 'tipo' => 'camion', 'voltaje' => '12V', 'capacidad_ah' => 180, 'stock_minimo' => 6, 'active' => true]);
        $solar = Product::create(['sku' => 'BAT-SOL-100', 'barcode' => '7791234001001', 'name' => 'Batería Solar GEL 100Ah', 'marca' => 'Ultracell', 'tipo' => 'solar', 'voltaje' => '12V', 'capacidad_ah' => 100, 'stock_minimo' => 0, 'active' => true]);

        // ── Racks en el mapa ────────────────────────────────────────
        $rackA = WarehouseLocation::create(['nombre' => 'Rack A', 'codigo' => 'E-01', 'pos_x' => 40, 'pos_y' => 40, 'width' => 160, 'height' => 110, 'columnas' => 4, 'niveles' => 3]);
        $rackB = WarehouseLocation::create(['nombre' => 'Rack B', 'codigo' => 'E-02', 'pos_x' => 260, 'pos_y' => 40, 'width' => 160, 'height' => 110, 'columnas' => 4, 'niveles' => 3]);
        $motos = WarehouseLocation::create(['nombre' => 'Estante Motos', 'codigo' => 'E-03', 'pos_x' => 480, 'pos_y' => 40, 'width' => 140, 'height' => 100, 'columnas' => 2, 'niveles' => 2]);

        // ── Pallets (un tipo de batería por puesto) ────────────────
        // S4 en dos lotes/racks distintos: la demo de FIFO salta de rack.
        $palletS4Viejo = ProductLocation::create(['product_id' => $s4->id, 'warehouse_location_id' => $rackA->id, 'columna' => 1, 'nivel' => 1, 'lote' => 'L-2403', 'fecha_ingreso' => now()->subMonths(3)->toDateString(), 'cantidad' => 8]);
        ProductLocation::create(['product_id' => $s4->id, 'warehouse_location_id' => $rackB->id, 'columna' => 1, 'nivel' => 1, 'lote' => 'L-2406', 'fecha_ingreso' => now()->subWeeks(2)->toDateString(), 'cantidad' => 12]);

        $palletS5 = ProductLocation::create(['product_id' => $s5->id, 'warehouse_location_id' => $rackA->id, 'columna' => 2, 'nivel' => 1, 'lote' => 'L-2404', 'fecha_ingreso' => now()->subMonths(2)->toDateString(), 'cantidad' => 10]);
        $palletMoto = ProductLocation::create(['product_id' => $moto->id, 'warehouse_location_id' => $motos->id, 'columna' => 1, 'nivel' => 1, 'lote' => null, 'fecha_ingreso' => now()->subMonth()->toDateString(), 'cantidad' => 15]);

        // Camión bajo el stock mínimo (6): dispara la alerta del dashboard.
        ProductLocation::create(['product_id' => $camion->id, 'warehouse_location_id' => $rackA->id, 'columna' => 3, 'nivel' => 1, 'lote' => 'L-2402', 'fecha_ingreso' => now()->subMonths(4)->toDateString(), 'cantidad' => 4]);

        ProductLocation::create(['product_id' => $solar->id, 'warehouse_location_id' => $rackB->id, 'columna' => 2, 'nivel' => 2, 'lote' => null, 'fecha_ingreso' => now()->subWeeks(3)->toDateString(), 'cantidad' => 6]);

        // ── Órdenes en cada estado del flujo ───────────────────────

        // 1) Por preparar
        $porPreparar = $this->orden($jefe, 'Ferretería El Tornillo', 'retiro', OrderStatus::LIBERADO);
        $this->item($porPreparar, $s4, 3);
        $this->item($porPreparar, $moto, 1);
        $this->eventos($porPreparar, $jefe, ['creado', 'liberado']);

        // 2) Preparando, con 1 de 2 unidades ya escaneada
        $preparando = $this->orden($jefe, 'Transportes Vega Ltda.', 'despacho', OrderStatus::PREPARANDO);
        $itemS5 = $this->item($preparando, $s5, 2, confirmada: 1);
        $palletS5->decrement('cantidad');
        OrderItemPick::create(['order_item_id' => $itemS5->id, 'product_location_id' => $palletS5->id, 'warehouse_location_id' => $rackA->id, 'cantidad' => 1]);
        $this->eventos($preparando, $jefe, ['creado', 'liberado']);
        $this->eventos($preparando, $bodeguero, ['preparando', 'escaneo']);

        // 3) Lista para entregar (picking completo)
        $lista = $this->orden($admin, 'Automotora Del Sur SpA', 'retiro', OrderStatus::LISTO);
        $itemS4 = $this->item($lista, $s4, 2, confirmada: 2);
        $palletS4Viejo->decrement('cantidad', 2);
        OrderItemPick::create(['order_item_id' => $itemS4->id, 'product_location_id' => $palletS4Viejo->id, 'warehouse_location_id' => $rackA->id, 'cantidad' => 2]);
        $this->eventos($lista, $admin, ['creado', 'liberado']);
        $this->eventos($lista, $bodeguero, ['preparando', 'escaneo', 'escaneo', 'listo']);

        // 4) Entregada ayer
        $entregada = $this->orden($jefe, 'Juan Carrasco', 'retiro', OrderStatus::ENTREGADO, [
            'retirado_por_nombre' => 'Juan Carrasco',
            'retirado_por_rut' => '12.345.678-9',
        ]);
        $itemMoto = $this->item($entregada, $moto, 1, confirmada: 1);
        $palletMoto->decrement('cantidad');
        OrderItemPick::create(['order_item_id' => $itemMoto->id, 'product_location_id' => $palletMoto->id, 'warehouse_location_id' => $motos->id, 'cantidad' => 1]);
        $this->eventos($entregada, $jefe, ['creado', 'liberado']);
        $this->eventos($entregada, $bodeguero, ['preparando', 'escaneo', 'listo', 'entregado']);

        // 5) Cancelada sin escaneos
        $cancelada = $this->orden($jefe, 'Pedido duplicado - anular', 'despacho', OrderStatus::CANCELADO);
        $this->item($cancelada, $solar, 1);
        $this->eventos($cancelada, $jefe, ['creado', 'liberado', 'cancelado']);

        $this->command->info('Datos de demo creados: 5 baterías, 3 racks con pallets y 5 órdenes en distintos estados.');
    }

    private function usuario(string $rol): User
    {
        $role = Role::firstOrCreate(['name' => $rol]);

        return User::where('role_id', $role->id)->first()
            ?? User::factory()->create(['role_id' => $role->id, 'name' => "Demo {$rol}"]);
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function orden(User $creador, string $cliente, string $tipoEntrega, OrderStatus $estado, array $extra = []): Order
    {
        return Order::create(array_merge([
            'source_type' => 'manual',
            'cliente_nombre' => $cliente,
            'tipo_entrega' => $tipoEntrega,
            'estado' => $estado,
            'creado_por' => $creador->id,
            'liberado_por' => $creador->id,
            'fecha_liberacion' => now()->subDay(),
        ], $extra));
    }

    private function item(Order $order, Product $product, int $solicitada, int $confirmada = 0): OrderItem
    {
        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'producto_codigo' => $product->sku,
            'producto_nombre' => $product->name,
            'cantidad_solicitada' => $solicitada,
            'cantidad_confirmada' => $confirmada,
        ]);
    }

    /**
     * @param list<string> $tipos
     */
    private function eventos(Order $order, User $user, array $tipos): void
    {
        $descripciones = [
            'creado' => 'Orden creada.',
            'liberado' => 'Orden liberada automáticamente a bodega al crearse.',
            'preparando' => 'Orden en preparación.',
            'escaneo' => 'Producto escaneado en picking.',
            'listo' => 'Productos confirmados.',
            'entregado' => 'Orden entregada.',
            'cancelado' => 'Orden cancelada.',
        ];

        foreach ($tipos as $tipo) {
            OrderEvent::create([
                'order_id' => $order->id,
                'tipo_evento' => $tipo,
                'descripcion' => $descripciones[$tipo] ?? $tipo,
                'user_id' => $user->id,
            ]);
        }
    }
}
