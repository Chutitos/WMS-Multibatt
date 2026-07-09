<?php

namespace Tests\Feature;

use App\Models\ProductLocationEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

/**
 * El jefe de bodega administra la ESTRUCTURA FÍSICA (estantes,
 * existencias, historial) pero no el SISTEMA (usuarios, catálogo,
 * ERP, eliminaciones).
 */
class JefeBodegaPermissionsTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_jefe_puede_crear_estantes_desde_el_mapa(): void
    {
        $jefe = $this->makeUser('jefe_bodega');

        $this->actingAs($jefe)->postJson('/ubicaciones', [])
            ->assertOk()
            ->assertJsonPath('codigo', 'E-01');
    }

    public function test_jefe_puede_renombrar_mover_y_desactivar_estantes(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $rack = $this->makeLocation(['nombre' => 'Estante 1']);

        $this->actingAs($jefe)->patchJson("/ubicaciones/{$rack->id}", [
            'nombre' => 'Zona norte',
            'pos_x' => 300,
            'pos_y' => 120,
        ])->assertOk();

        $rack->refresh();
        $this->assertSame('Zona norte', $rack->nombre);
        $this->assertSame(300, $rack->pos_x);

        $this->actingAs($jefe)->patchJson("/ubicaciones/{$rack->id}", ['activa' => false])->assertOk();
        $this->assertFalse($rack->fresh()->activa);
    }

    public function test_jefe_puede_cambiar_dimensiones_del_rack(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $rack = $this->makeLocation();

        $this->actingAs($jefe)->patch("/ubicaciones/{$rack->id}", [
            'columnas' => 5,
            'niveles' => 2,
        ])->assertRedirect("/ubicaciones/{$rack->id}");

        $this->assertSame(5, $rack->fresh()->columnas);
    }

    public function test_jefe_puede_asignar_y_corregir_existencias_con_historial(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $product = $this->makeProduct();
        $rack = $this->makeLocation();

        $this->actingAs($jefe)->post('/existencias', [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 1,
            'nivel' => 1,
            'fecha_ingreso' => now()->toDateString(),
            'cantidad' => 6,
        ])->assertRedirect('/existencias');

        $pallet = $product->productLocations()->first();

        $this->actingAs($jefe)->patch("/existencias/{$pallet->id}", [
            'product_id' => $product->id,
            'warehouse_location_id' => $rack->id,
            'columna' => 2,
            'nivel' => 1,
            'fecha_ingreso' => $pallet->fecha_ingreso->toDateString(),
            'cantidad' => 4,
        ])->assertRedirect('/existencias');

        // Toda corrección del jefe queda auditada con su usuario.
        $eventos = ProductLocationEvent::where('user_id', $jefe->id)->pluck('accion');
        $this->assertTrue($eventos->contains('creada'));
        $this->assertTrue($eventos->contains('editada'));
    }

    public function test_jefe_puede_ver_existencias_y_su_historial(): void
    {
        $jefe = $this->makeUser('jefe_bodega');

        $this->actingAs($jefe)->get('/existencias')->assertOk()->assertSee('Historial');
        $this->actingAs($jefe)->get('/existencias/historial')->assertOk();
    }

    public function test_jefe_ve_el_mapa_con_herramientas_de_edicion(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $this->makeLocation();

        $this->actingAs($jefe)->get('/ubicaciones')
            ->assertOk()
            ->assertSee('Crear estante')
            ->assertSee('Arrastra las ubicaciones');
    }

    public function test_jefe_no_puede_administrar_el_sistema(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $product = $this->makeProduct();
        $rack = $this->makeLocation();
        $pallet = $this->stockProductAt($product, $rack, 5);

        // Usuarios y roles: no.
        $this->actingAs($jefe)->get('/usuarios')->assertForbidden();
        $this->actingAs($jefe)->post('/usuarios', [])->assertForbidden();

        // Catálogo de baterías: no.
        $this->actingAs($jefe)->get('/productos')->assertForbidden();

        // Credenciales/estado del ERP: no.
        $this->actingAs($jefe)->get('/integracion')->assertForbidden();

        // Eliminar existencias: solo admin.
        $this->actingAs($jefe)->delete("/existencias/{$pallet->id}")->assertForbidden();
        $this->assertNotNull($pallet->fresh());
    }

    public function test_bodeguero_no_puede_cambiar_la_estructura_de_bodega(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $rack = $this->makeLocation();

        $this->actingAs($bodeguero)->postJson('/ubicaciones', [])->assertForbidden();
        $this->actingAs($bodeguero)->patchJson("/ubicaciones/{$rack->id}", ['nombre' => 'Hackeado'])->assertForbidden();
        $this->actingAs($bodeguero)->get('/existencias/historial')->assertForbidden();

        // El mapa le queda en modo lectura, sin botón de crear.
        $this->actingAs($bodeguero)->get('/ubicaciones')
            ->assertOk()
            ->assertDontSee('Crear estante');
    }

    public function test_admin_conserva_acceso_total(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->get('/usuarios')->assertOk();
        $this->actingAs($admin)->get('/productos')->assertOk();
        $this->actingAs($admin)->get('/integracion')->assertOk();
        $this->actingAs($admin)->get('/existencias/historial')->assertOk();
        $this->actingAs($admin)->postJson('/ubicaciones', [])->assertOk();
    }
}
