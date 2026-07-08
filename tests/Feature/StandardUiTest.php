<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

/**
 * Verifica que las pantallas estandarizadas rendericen con el sistema
 * de componentes (x-wms.*) sin errores de Blade.
 */
class StandardUiTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_pantallas_de_admin_renderizan(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeProduct(['name' => 'Batería de prueba']);

        $this->actingAs($admin)->get('/usuarios')->assertOk()->assertSee('Crear usuario');
        $this->actingAs($admin)->get('/usuarios/create')->assertOk()->assertSee('Guardar usuario');
        $this->actingAs($admin)->get('/productos')->assertOk()->assertSee('Batería de prueba');
        $this->actingAs($admin)->get('/productos/create')->assertOk()->assertSee('Guardar producto');
        $this->actingAs($admin)->get('/orders')->assertOk()->assertSee('Crear orden');
        $this->actingAs($admin)->get('/orders/create')->assertOk()->assertSee('Guardar orden');
        $this->actingAs($admin)->get('/existencias/create')->assertOk()->assertSee('Asignar existencia');
    }

    public function test_dashboard_de_jefe_renderiza_con_tarjetas_clickeables(): void
    {
        $jefe = $this->makeUser('jefe_bodega');

        $this->actingAs($jefe)->get('/dashboard')
            ->assertOk()
            ->assertSee('Por preparar')
            ->assertSee('Para entregar')
            ->assertSee('Crear orden');
    }

    public function test_editar_usuario_renderiza_con_datos(): void
    {
        $admin = $this->makeUser('admin');
        $otro = $this->makeUser('bodeguero');

        $this->actingAs($admin)->get("/usuarios/{$otro->id}/edit")
            ->assertOk()
            ->assertSee($otro->name)
            ->assertSee('Guardar cambios');
    }

    public function test_navegacion_muestra_pestanas_segun_rol(): void
    {
        $admin = $this->makeUser('admin');
        $bodeguero = $this->makeUser('bodeguero');

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Usuarios')
            ->assertSee('Existencias');

        $this->actingAs($bodeguero)->get('/dashboard')
            ->assertOk()
            ->assertSee('Por preparar')
            ->assertDontSee('Usuarios');
    }
}
