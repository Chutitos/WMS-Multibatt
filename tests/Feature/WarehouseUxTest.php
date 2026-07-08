<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class WarehouseUxTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    private function makeOrder(User $creador, Product $product, OrderStatus $estado, int $solicitada = 3, int $confirmada = 0): Order
    {
        $order = Order::create([
            'source_type' => 'manual',
            'cliente_nombre' => 'Cliente de prueba',
            'tipo_entrega' => 'retiro',
            'estado' => $estado,
            'creado_por' => $creador->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'producto_codigo' => $product->sku,
            'producto_nombre' => $product->name,
            'cantidad_solicitada' => $solicitada,
            'cantidad_confirmada' => $confirmada,
        ]);

        return $order;
    }

    public function test_admin_crea_estante_con_un_clic_y_codigo_autoincremental(): void
    {
        $admin = $this->makeUser('admin');

        $primera = $this->actingAs($admin)->postJson('/ubicaciones', []);
        $primera->assertOk()
            ->assertJsonPath('codigo', 'E-01')
            ->assertJsonPath('nombre', 'Estante 1');

        $segunda = $this->actingAs($admin)->postJson('/ubicaciones', []);
        $segunda->assertOk()->assertJsonPath('codigo', 'E-02');

        // Los estantes creados con un clic no quedan apilados en el mismo punto.
        $this->assertNotSame(
            $primera->json('pos_x') . '-' . $primera->json('pos_y'),
            $segunda->json('pos_x') . '-' . $segunda->json('pos_y'),
        );
    }

    public function test_codigo_autoincremental_salta_codigos_ya_ocupados(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeLocation(['codigo' => 'E-07']);

        $this->actingAs($admin)->postJson('/ubicaciones', [])
            ->assertOk()
            ->assertJsonPath('codigo', 'E-08');
    }

    public function test_crear_estante_con_nombre_y_codigo_manual_sigue_funcionando(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->postJson('/ubicaciones', [
            'nombre' => 'Rack de baterías',
            'codigo' => 'RACK-01',
        ])->assertOk()
            ->assertJsonPath('codigo', 'RACK-01')
            ->assertJsonPath('nombre', 'Rack de baterías');
    }

    public function test_admin_puede_renombrar_ubicacion(): void
    {
        $admin = $this->makeUser('admin');
        $location = $this->makeLocation(['nombre' => 'Estante 1']);

        $this->actingAs($admin)->patchJson("/ubicaciones/{$location->id}", ['nombre' => 'Zona de baterías'])
            ->assertOk();

        $this->assertSame('Zona de baterías', $location->fresh()->nombre);
    }

    public function test_pantalla_por_preparar_muestra_boton_grande(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $this->makeOrder($bodeguero, $product, OrderStatus::LIBERADO);

        $this->actingAs($bodeguero)->get('/bodega')
            ->assertOk()
            ->assertSee('Comenzar a preparar')
            ->assertSee('Cliente de prueba');
    }

    public function test_pantalla_preparando_muestra_avance_y_boton_de_escaneo(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $this->makeOrder($bodeguero, $product, OrderStatus::PREPARANDO, solicitada: 3, confirmada: 1);

        $this->actingAs($bodeguero)->get('/bodega/preparando')
            ->assertOk()
            ->assertSee('Escaneados 1 de 3')
            ->assertSee('Escanear productos');
    }

    public function test_pantalla_preparando_ofrece_confirmar_cuando_el_escaneo_esta_completo(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $this->makeOrder($bodeguero, $product, OrderStatus::PREPARANDO, solicitada: 3, confirmada: 3);

        $this->actingAs($bodeguero)->get('/bodega/preparando')
            ->assertOk()
            ->assertSee('Escaneo completo')
            ->assertSee('Confirmar que está lista')
            ->assertDontSee('Escanear productos');
    }

    public function test_pantalla_para_entregar_muestra_boton_entregar(): void
    {
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $this->makeOrder($bodeguero, $product, OrderStatus::LISTO, solicitada: 3, confirmada: 3);

        $this->actingAs($bodeguero)->get('/bodega/listo')
            ->assertOk()
            ->assertSee('Entregar');
    }
}
