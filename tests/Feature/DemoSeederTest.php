<?php

namespace Tests\Feature;

use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_crea_un_escenario_completo_y_es_reejecutable(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertDatabaseCount('products', 5);
        $this->assertDatabaseCount('warehouse_locations', 3);
        $this->assertDatabaseCount('orders', 5);
        $this->assertDatabaseCount('product_locations', 6);
        $this->assertDatabaseCount('order_item_picks', 3);

        // Una orden por cada estado del flujo.
        foreach (['liberado', 'preparando', 'listo', 'entregado', 'cancelado'] as $estado) {
            $this->assertDatabaseHas('orders', ['estado' => $estado]);
        }

        // Re-ejecutarlo no duplica nada.
        $this->seed(DemoSeeder::class);
        $this->assertDatabaseCount('products', 5);
        $this->assertDatabaseCount('orders', 5);
    }
}
