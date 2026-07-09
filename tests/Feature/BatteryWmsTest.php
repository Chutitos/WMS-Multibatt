<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class BatteryWmsTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_admin_crea_bateria_con_ficha_tecnica(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post('/productos', [
            'sku' => 'BAT-75AH',
            'name' => 'Batería 75Ah borne estándar',
            'marca' => 'Bosch',
            'tipo' => 'auto',
            'voltaje' => '12V',
            'capacidad_ah' => 75,
            'meses_recarga' => 6,
            'stock_minimo' => 4,
            'active' => 1,
        ])->assertRedirect('/productos');

        $product = Product::where('sku', 'BAT-75AH')->first();
        $this->assertSame('Bosch', $product->marca);
        $this->assertSame('Auto', $product->tipoLabel());
        $this->assertSame('12V · 75Ah · Bosch', $product->fichaCorta());
        $this->assertSame(4, $product->stock_minimo);
    }

    public function test_tipo_de_bateria_invalido_es_rechazado(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post('/productos', [
            'sku' => 'BAT-X',
            'name' => 'Batería X',
            'tipo' => 'lavadora',
            'meses_recarga' => 6,
            'stock_minimo' => 0,
        ])->assertSessionHasErrors('tipo');
    }

    public function test_busqueda_de_productos_por_marca_y_tipo(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeProduct(['name' => 'Batería camión 180Ah', 'marca' => 'Yuasa', 'tipo' => 'camion']);
        $this->makeProduct(['name' => 'Batería moto 7Ah', 'marca' => 'Bosch', 'tipo' => 'moto']);

        $this->actingAs($admin)->get('/productos?q=Yuasa')
            ->assertOk()
            ->assertSee('Batería camión 180Ah')
            ->assertDontSee('Batería moto 7Ah');

        $this->actingAs($admin)->get('/productos?tipo=moto')
            ->assertOk()
            ->assertSee('Batería moto 7Ah')
            ->assertDontSee('Batería camión 180Ah');
    }

    public function test_pallet_antiguo_aparece_como_por_recargar(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['name' => 'Batería vieja']); // meses_recarga default 6
        $productFresco = $this->makeProduct(['name' => 'Batería fresca']);
        $rack = $this->makeLocation();

        $this->stockProductAt($product, $rack, 5, now()->subMonths(7)->toDateString());
        $this->stockProductAt($productFresco, $rack, 5, now()->subMonth()->toDateString());

        // Badge en el listado completo.
        $this->actingAs($bodeguero)->get('/existencias')
            ->assertOk()
            ->assertSee('⚡ Recargar');

        // El filtro deja solo la antigua.
        $this->actingAs($bodeguero)->get('/existencias?recarga=1')
            ->assertOk()
            ->assertSee('Batería vieja')
            ->assertDontSee('Batería fresca');
    }

    public function test_pallet_agotado_no_cuenta_para_recarga(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['name' => 'Batería agotada antigua']);
        $rack = $this->makeLocation();
        $this->stockProductAt($product, $rack, 0, now()->subMonths(12)->toDateString());

        $this->actingAs($bodeguero)->get('/existencias?recarga=1')
            ->assertOk()
            ->assertDontSee('Batería agotada antigua');
    }

    public function test_dashboard_bodeguero_avisa_pallets_por_recargar(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $rack = $this->makeLocation();
        $this->stockProductAt($product, $rack, 5, now()->subMonths(8)->toDateString());

        $this->actingAs($bodeguero)->get('/dashboard')
            ->assertOk()
            ->assertSee('necesita recarga');
    }

    public function test_dashboard_jefe_avisa_baterias_bajo_stock_minimo(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $product = $this->makeProduct(['stock_minimo' => 10]);
        $rack = $this->makeLocation();
        $this->stockProductAt($product, $rack, 3);

        $this->actingAs($jefe)->get('/dashboard')
            ->assertOk()
            ->assertSee('bajo el stock mínimo');
    }

    public function test_catalogo_marca_baterias_bajo_el_minimo(): void
    {
        $admin = $this->makeUser('admin');
        $product = $this->makeProduct(['name' => 'Batería crítica', 'stock_minimo' => 10]);
        $rack = $this->makeLocation();
        $this->stockProductAt($product, $rack, 2);

        $this->actingAs($admin)->get('/productos')
            ->assertOk()
            ->assertSee('Bajo el mínimo (10)');
    }

    public function test_busqueda_de_existencias_por_texto(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $productA = $this->makeProduct(['name' => 'Batería náutica 100Ah']);
        $productB = $this->makeProduct(['name' => 'Batería moto pequeña']);
        $rackNorte = $this->makeLocation(['nombre' => 'Rack Norte']);
        $rackSur = $this->makeLocation(['nombre' => 'Rack Sur']);

        $this->stockProductAt($productA, $rackNorte, 5);
        $this->stockProductAt($productB, $rackSur, 5);

        $this->actingAs($bodeguero)->get('/existencias?q=náutica')
            ->assertOk()
            ->assertSee('Batería náutica 100Ah')
            ->assertDontSee('Batería moto pequeña');

        $this->actingAs($bodeguero)->get('/existencias?q=Rack Sur')
            ->assertOk()
            ->assertSee('Batería moto pequeña')
            ->assertDontSee('Batería náutica 100Ah');
    }

    public function test_papeleta_y_comprobante_imprimibles(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['name' => 'Batería 60Ah', 'voltaje' => '12V', 'capacidad_ah' => 60]);

        $order = Order::create([
            'source_type' => 'manual',
            'cliente_nombre' => 'Cliente Imprenta',
            'tipo_entrega' => 'retiro',
            'estado' => OrderStatus::PREPARANDO,
            'creado_por' => $bodeguero->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'producto_codigo' => $product->sku,
            'producto_nombre' => $product->name,
            'cantidad_solicitada' => 2,
            'cantidad_confirmada' => 0,
        ]);

        // En preparación: papeleta.
        $this->actingAs($bodeguero)->get("/orders/{$order->id}/imprimir")
            ->assertOk()
            ->assertSee('PAPELETA DE PREPARACIÓN')
            ->assertSee('Cliente Imprenta')
            ->assertSee('Batería 60Ah');

        // Entregada: comprobante con quién retiró.
        $order->update([
            'estado' => OrderStatus::ENTREGADO,
            'retirado_por_nombre' => 'María Retiro',
        ]);

        $this->actingAs($bodeguero)->get("/orders/{$order->id}/imprimir")
            ->assertOk()
            ->assertSee('COMPROBANTE DE ENTREGA')
            ->assertSee('María Retiro');
    }
}
