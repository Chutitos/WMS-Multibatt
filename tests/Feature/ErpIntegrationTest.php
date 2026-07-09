<?php

namespace Tests\Feature;

use App\Models\ErpDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class ErpIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_pagina_de_integracion_es_solo_para_admin(): void
    {
        $admin = $this->makeUser('admin');
        $jefe = $this->makeUser('jefe_bodega');
        $bodeguero = $this->makeUser('bodeguero');

        $this->actingAs($admin)->get('/integracion')->assertOk();
        $this->actingAs($jefe)->get('/integracion')->assertForbidden();
        $this->actingAs($bodeguero)->get('/integracion')->assertForbidden();
    }

    public function test_sin_credenciales_muestra_no_configurado(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->get('/integracion')
            ->assertOk()
            ->assertSee('No configurado')
            ->assertSee('DEFONTANA_API_KEY')
            ->assertSee('Todavía no hay documentos del ERP');
    }

    public function test_con_credenciales_muestra_configurado_sin_conexion(): void
    {
        config(['defontana.base_url' => 'https://api.defontana.com', 'defontana.api_key' => 'token-prueba']);

        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->get('/integracion')
            ->assertOk()
            ->assertSee('Configurado, sin conexión');
    }

    public function test_lista_documentos_erp_con_su_estado_de_sync(): void
    {
        $admin = $this->makeUser('admin');

        ErpDocument::create([
            'external_id' => 'DF-0001',
            'tipo_documento' => 'factura',
            'numero_documento' => 'F-12345',
            'cliente_nombre' => 'Cliente ERP',
            'fecha_documento' => now()->toDateString(),
            'estado_sync' => 'failed',
            'imported_by' => $admin->id,
            'attempts' => 3,
            'last_error' => 'Timeout de conexión',
        ]);

        $this->actingAs($admin)->get('/integracion')
            ->assertOk()
            ->assertSee('F-12345')
            ->assertSee('Cliente ERP')
            ->assertSee('Timeout de conexión')
            ->assertSee('3 intentos');
    }

    public function test_comando_sync_falla_claramente_sin_credenciales(): void
    {
        $this->artisan('defontana:sync')
            ->expectsOutputToContain('no está configurado')
            ->assertExitCode(1);
    }

    public function test_comando_sync_corre_con_credenciales_aunque_sin_api_real(): void
    {
        config(['defontana.base_url' => 'https://api.defontana.com', 'defontana.api_key' => 'token-prueba']);

        $this->artisan('defontana:sync')
            ->expectsOutputToContain('Documentos importados: 0')
            ->assertExitCode(0);
    }
}
