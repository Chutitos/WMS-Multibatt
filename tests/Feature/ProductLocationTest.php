<?php

namespace Tests\Feature;

use App\Models\ProductLocationEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class ProductLocationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_no_se_puede_asignar_existencia_a_ubicacion_inactiva(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation(['activa' => false]);

        $response = $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ]);

        $response->assertSessionHasErrors('warehouse_location_id');
        $this->assertDatabaseCount('product_locations', 0);
    }

    public function test_no_se_puede_asignar_existencia_de_producto_inactivo(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct(['active' => false]);
        $location = $this->makeLocation();

        $response = $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ]);

        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseCount('product_locations', 0);
    }

    public function test_crear_existencia_registra_evento_de_trazabilidad(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();

        $this->actingAs($bodeguero)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 5,
        ])->assertRedirect('/existencias');

        $evento = ProductLocationEvent::first();
        $this->assertNotNull($evento);
        $this->assertSame('creada', $evento->accion);
        $this->assertSame($bodeguero->id, $evento->user_id);
    }

    public function test_editar_existencia_registra_evento_con_el_cambio(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $pl = $this->stockProductAt($product, $location, 10);

        $this->actingAs($bodeguero)->patch("/existencias/{$pl->id}", [
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'lote' => $pl->lote,
            'fecha_ingreso' => $pl->fecha_ingreso->toDateString(),
            'cantidad' => 7,
        ])->assertRedirect('/existencias');

        $evento = ProductLocationEvent::where('accion', 'editada')->first();
        $this->assertNotNull($evento);
        $this->assertStringContainsString('cantidad: 10 → 7', $evento->detalle);
        $this->assertSame(7, $pl->fresh()->cantidad);
    }

    public function test_editar_sin_cambios_no_registra_evento(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $pl = $this->stockProductAt($product, $location, 10);

        $this->actingAs($bodeguero)->patch("/existencias/{$pl->id}", [
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'lote' => $pl->lote,
            'fecha_ingreso' => $pl->fecha_ingreso->toDateString(),
            'cantidad' => 10,
        ])->assertRedirect('/existencias');

        $this->assertDatabaseCount('product_location_events', 0);
    }

    public function test_no_se_puede_mover_existencia_a_ubicacion_inactiva(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $inactiva = $this->makeLocation(['activa' => false]);
        $pl = $this->stockProductAt($product, $location, 10);

        $response = $this->actingAs($bodeguero)->patch("/existencias/{$pl->id}", [
            'product_id' => $product->id,
            'warehouse_location_id' => $inactiva->id,
            'fecha_ingreso' => $pl->fecha_ingreso->toDateString(),
            'cantidad' => 10,
        ]);

        $response->assertSessionHasErrors('warehouse_location_id');
        $this->assertSame($location->id, $pl->fresh()->warehouse_location_id);
    }

    public function test_se_puede_editar_existencia_cuya_ubicacion_actual_esta_inactiva(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $inactiva = $this->makeLocation(['activa' => false]);
        $pl = $this->stockProductAt($product, $inactiva, 10);

        // Mantener la ubicación inactiva actual es válido: solo se corrige la cantidad.
        $this->actingAs($bodeguero)->patch("/existencias/{$pl->id}", [
            'product_id' => $product->id,
            'warehouse_location_id' => $inactiva->id,
            'fecha_ingreso' => $pl->fecha_ingreso->toDateString(),
            'cantidad' => 3,
        ])->assertRedirect('/existencias');

        $this->assertSame(3, $pl->fresh()->cantidad);
    }

    public function test_bodeguero_no_puede_eliminar_existencias(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $pl = $this->stockProductAt($product, $location, 10);

        $this->actingAs($bodeguero)->delete("/existencias/{$pl->id}")->assertForbidden();
        $this->assertNotNull($pl->fresh());
    }

    public function test_admin_elimina_existencia_y_queda_evento_con_snapshot(): void
    {
        $admin = $this->makeUser('admin');
        $product = $this->makeProduct(['name' => 'Batería 12V']);
        $location = $this->makeLocation();
        $pl = $this->stockProductAt($product, $location, 10);

        $this->actingAs($admin)->delete("/existencias/{$pl->id}")->assertRedirect('/existencias');

        $this->assertNull($pl->fresh());

        $evento = ProductLocationEvent::where('accion', 'eliminada')->first();
        $this->assertNotNull($evento);
        $this->assertStringContainsString('Batería 12V', $evento->detalle);
        $this->assertSame($admin->id, $evento->user_id);
    }

    public function test_listado_renderiza_con_acciones_segun_rol(): void
    {
        $admin = $this->makeUser('admin');
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $this->stockProductAt($product, $location, 5);

        $this->actingAs($admin)->get('/existencias')
            ->assertOk()
            ->assertSee('Historial')
            ->assertSee('Eliminar');

        $this->actingAs($bodeguero)->get('/existencias')
            ->assertOk()
            ->assertSee('Editar')
            ->assertDontSee('Eliminar');
    }

    public function test_mapa_muestra_ubicacion_inactiva_marcada(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeLocation(['nombre' => 'Estante fantasma', 'activa' => false]);

        $this->actingAs($admin)->get('/ubicaciones')
            ->assertOk()
            ->assertSee('Estante fantasma')
            ->assertSee('(inactiva)');
    }

    public function test_formulario_edicion_muestra_ubicacion_inactiva_actual(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $inactiva = $this->makeLocation(['nombre' => 'Bodega vieja', 'activa' => false]);
        $pl = $this->stockProductAt($product, $inactiva, 5);

        $this->actingAs($bodeguero)->get("/existencias/{$pl->id}/edit")
            ->assertOk()
            ->assertSee('Bodega vieja')
            ->assertSee('(inactiva)');
    }

    public function test_historial_es_solo_para_admin(): void
    {
        $admin = $this->makeUser('admin');
        $bodeguero = $this->makeUser('bodeguero');

        $this->actingAs($admin)->get('/existencias/historial')->assertOk();
        $this->actingAs($bodeguero)->get('/existencias/historial')->assertForbidden();
    }

    public function test_no_se_puede_desactivar_ubicacion_con_stock(): void
    {
        $admin = $this->makeUser('admin');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $this->stockProductAt($product, $location, 5);

        $response = $this->actingAs($admin)->patchJson("/ubicaciones/{$location->id}", ['activa' => false]);

        $response->assertStatus(422);
        $this->assertTrue($location->fresh()->activa);
    }

    public function test_se_puede_desactivar_ubicacion_vacia_y_reactivarla(): void
    {
        $admin = $this->makeUser('admin');
        $location = $this->makeLocation();

        $this->actingAs($admin)->patchJson("/ubicaciones/{$location->id}", ['activa' => false])->assertOk();
        $this->assertFalse($location->fresh()->activa);

        $this->actingAs($admin)->patchJson("/ubicaciones/{$location->id}", ['activa' => true])->assertOk();
        $this->assertTrue($location->fresh()->activa);
    }
}
