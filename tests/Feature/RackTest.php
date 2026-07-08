<?php

namespace Tests\Feature;

use App\Models\ProductLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class RackTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_se_puede_asignar_pallet_a_un_puesto_del_rack(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $rack = $this->makeLocation(['columnas' => 4, 'niveles' => 3]);

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 2,
            'nivel' => 3,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 8,
        ])->assertRedirect('/existencias');

        $pallet = ProductLocation::first();
        $this->assertSame(2, $pallet->columna);
        $this->assertSame(3, $pallet->nivel);
    }

    public function test_asignar_desde_la_grilla_vuelve_al_estante(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $rack = $this->makeLocation();

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
            'volver_al_estante' => 1,
        ])->assertRedirect("/ubicaciones/{$rack->id}");
    }

    public function test_no_se_puede_poner_dos_pallets_en_el_mismo_puesto(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $productA = $this->makeProduct();
        $productB = $this->makeProduct();
        $rack = $this->makeLocation();

        ProductLocation::create([
            'product_id' => $productA->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ]);

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $productB->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 3,
        ])->assertSessionHasErrors('columna');

        $this->assertDatabaseCount('product_locations', 1);
    }

    public function test_un_puesto_agotado_se_puede_reocupar(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $productA = $this->makeProduct();
        $productB = $this->makeProduct();
        $rack = $this->makeLocation();

        ProductLocation::create([
            'product_id' => $productA->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 0,
        ]);

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $productB->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 4,
        ])->assertRedirect('/existencias');
    }

    public function test_no_se_puede_asignar_a_un_puesto_que_no_existe_en_el_rack(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $rack = $this->makeLocation(['columnas' => 2, 'niveles' => 2]);

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 3,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ])->assertSessionHasErrors('columna');
    }

    public function test_columna_sin_nivel_es_invalido(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $rack = $this->makeLocation();

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ])->assertSessionHasErrors('nivel');
    }

    public function test_admin_puede_cambiar_dimensiones_del_rack(): void
    {
        $admin = $this->makeUser('admin');
        $rack = $this->makeLocation();

        $this->actingAs($admin)->patch("/ubicaciones/{$rack->id}", [
            'columnas' => 6,
            'niveles' => 4,
        ])->assertRedirect("/ubicaciones/{$rack->id}");

        $rack->refresh();
        $this->assertSame(6, $rack->columnas);
        $this->assertSame(4, $rack->niveles);
    }

    public function test_no_se_puede_achicar_rack_con_pallets_fuera_del_nuevo_tamano(): void
    {
        $admin = $this->makeUser('admin');
        $product = $this->makeProduct();
        $rack = $this->makeLocation(['columnas' => 4, 'niveles' => 3]);

        ProductLocation::create([
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 4,
            'nivel' => 3,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ]);

        $this->actingAs($admin)->patchJson("/ubicaciones/{$rack->id}", [
            'columnas' => 2,
        ])->assertStatus(422);

        $this->assertSame(4, $rack->fresh()->columnas);
    }

    public function test_detalle_del_rack_muestra_grilla_con_pallets_y_puestos_libres(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['name' => 'Batería 60Ah']);
        $rack = $this->makeLocation(['nombre' => 'Rack Norte', 'columnas' => 2, 'niveles' => 2]);

        ProductLocation::create([
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 2,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 12,
        ]);

        $this->actingAs($bodeguero)->get("/ubicaciones/{$rack->id}")
            ->assertOk()
            ->assertSee('Rack Norte')
            ->assertSee('Batería 60Ah')
            ->assertSee('12 unidades')
            ->assertSee('+ Asignar');
    }

    public function test_existencia_sin_puesto_aparece_como_pendiente_de_ubicar(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['name' => 'Batería suelta']);
        $rack = $this->makeLocation();

        $this->stockProductAt($product, $rack, 7);

        $this->actingAs($bodeguero)->get("/ubicaciones/{$rack->id}")
            ->assertOk()
            ->assertSee('sin puesto asignado')
            ->assertSee('Batería suelta')
            ->assertSee('Ubicar en un puesto');
    }

    public function test_escanear_informa_el_puesto_del_pallet(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['barcode' => 'BAT-999']);
        $rack = $this->makeLocation(['nombre' => 'Rack Sur']);

        ProductLocation::create([
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 2,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ]);

        $order = \App\Models\Order::create([
            'source_type' => 'manual',
            'cliente_nombre' => 'Cliente rack',
            'tipo_entrega' => 'retiro',
            'estado' => \App\Enums\OrderStatus::PREPARANDO,
            'creado_por' => $bodeguero->id,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'producto_codigo' => $product->sku,
            'producto_nombre' => $product->name,
            'cantidad_solicitada' => 1,
            'cantidad_confirmada' => 0,
        ]);

        $respuesta = $this->actingAs($bodeguero)
            ->postJson("/orders/{$order->id}/picking/escanear", ['codigo' => 'BAT-999']);

        $respuesta->assertOk();
        $this->assertStringContainsString('columna 2, nivel 1', $respuesta->json('message'));
    }
}
