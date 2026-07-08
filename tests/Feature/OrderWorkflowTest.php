<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_full_order_lifecycle_with_fifo_picking(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $bodeguero = $this->makeUser('bodeguero');

        $product = $this->makeProduct(['sku' => 'BAT-01']);
        $locationOld = $this->makeLocation(['codigo' => 'A-01']);
        $locationNew = $this->makeLocation(['codigo' => 'A-02']);

        $this->stockProductAt($product, $locationOld, 5, '2026-01-01');
        $this->stockProductAt($product, $locationNew, 10, '2026-02-01');

        $this->actingAs($jefe)->post('/orders', [
            'cliente_nombre' => 'Cliente Test',
            'tipo_entrega' => 'retiro',
            'productos' => [
                ['product_id' => $product->id, 'cantidad' => 7],
            ],
        ])->assertRedirect(route('orders.index'));

        // Las órdenes nacen liberadas: van directo a "Por preparar".
        $order = Order::latest('id')->first();
        $this->assertSame('liberado', $order->estado->value);
        $this->assertNotNull($order->fecha_liberacion);
        $this->assertSame($jefe->id, $order->liberado_por);

        // Bodeguero puede verla y trabajarla de inmediato.
        $this->actingAs($bodeguero)->get("/orders/{$order->id}")->assertOk();
        $this->actingAs($bodeguero)->get('/bodega')->assertOk()->assertSee('Cliente Test');

        $this->actingAs($bodeguero)->patch("/orders/{$order->id}/preparar")->assertRedirect();
        $this->assertSame('preparando', $order->fresh()->estado->value);

        // Confirmar sin haber escaneado debe rebotar a picking, no avanzar de estado.
        $this->actingAs($bodeguero)->patch("/orders/{$order->id}/confirmar")
            ->assertRedirect("/orders/{$order->id}/picking");
        $this->assertSame('preparando', $order->fresh()->estado->value);

        for ($i = 0; $i < 7; $i++) {
            $this->actingAs($bodeguero)
                ->postJson("/orders/{$order->id}/picking/escanear", ['codigo' => 'BAT-01'])
                ->assertOk();
        }

        $item = $order->fresh()->items->first();
        $this->assertSame(7, $item->cantidad_confirmada);

        // FIFO: se consumen primero las 5 unidades del lote más antiguo (A-01),
        // luego 2 del lote más nuevo (A-02), que queda en 10-2=8.
        $this->assertSame(0, (int) $locationOld->productLocations()->sum('cantidad'));
        $this->assertSame(8, (int) $locationNew->productLocations()->sum('cantidad'));

        $this->actingAs($bodeguero)->patch("/orders/{$order->id}/confirmar")
            ->assertRedirect(route('bodega.listo'));
        $this->assertSame('listo', $order->fresh()->estado->value);

        // Entregar (retiro) exige el nombre de quien retira.
        $this->actingAs($bodeguero)->patch("/orders/{$order->id}/entregar")
            ->assertSessionHasErrors('retirado_por_nombre');
        $this->assertSame('listo', $order->fresh()->estado->value);

        $this->actingAs($bodeguero)->patch("/orders/{$order->id}/entregar", [
            'retirado_por_nombre' => 'Juan Pérez',
        ])->assertRedirect(route('bodega.listo'));

        $order->refresh();
        $this->assertSame('entregado', $order->estado->value);
        $this->assertSame('Juan Pérez', $order->retirado_por_nombre);

        // Trazabilidad: creado, liberado, preparando, 7 escaneos, listo, entregado.
        $this->assertSame(12, OrderEvent::where('order_id', $order->id)->count());
    }

    public function test_role_separation_between_orders_and_bodega(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $bodeguero = $this->makeUser('bodeguero');

        $this->actingAs($bodeguero)->get('/orders')->assertForbidden();
        $this->actingAs($jefe)->get('/bodega')->assertForbidden();
    }

    public function test_bodeguero_sigue_sin_ver_ordenes_antiguas_en_estado_creado(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $bodeguero = $this->makeUser('bodeguero');

        // Orden legada de antes del flujo automático: sigue protegida.
        $order = Order::create([
            'source_type' => 'manual',
            'cliente_nombre' => 'Cliente Legado',
            'tipo_entrega' => 'retiro',
            'estado' => \App\Enums\OrderStatus::CREADO,
            'creado_por' => $jefe->id,
        ]);

        $this->actingAs($bodeguero)->get("/orders/{$order->id}")->assertForbidden();
    }

    public function test_cancelar_solo_disponible_mientras_no_este_preparando(): void
    {
        $jefe = $this->makeUser('jefe_bodega');
        $bodeguero = $this->makeUser('bodeguero');
        $product = $this->makeProduct();
        $location = $this->makeLocation();
        $this->stockProductAt($product, $location, 5);

        $this->actingAs($jefe)->post('/orders', [
            'cliente_nombre' => 'Cliente Cancelar',
            'tipo_entrega' => 'retiro',
            'productos' => [['product_id' => $product->id, 'cantidad' => 1]],
        ]);
        $order = Order::latest('id')->first();

        $this->actingAs($jefe)->patch("/orders/{$order->id}/cancelar")
            ->assertRedirect(route('orders.index'));
        $this->assertSame('cancelado', $order->fresh()->estado->value);

        $this->actingAs($jefe)->post('/orders', [
            'cliente_nombre' => 'Cliente Cancelar 2',
            'tipo_entrega' => 'retiro',
            'productos' => [['product_id' => $product->id, 'cantidad' => 1]],
        ]);
        $order2 = Order::latest('id')->first();
        $this->assertSame('liberado', $order2->estado->value);

        $this->actingAs($bodeguero)->patch("/orders/{$order2->id}/preparar");
        $this->assertSame('preparando', $order2->fresh()->estado->value);

        // Una vez en preparación, jefe_bodega ya no puede cancelarla.
        $this->actingAs($jefe)->patch("/orders/{$order2->id}/cancelar")
            ->assertRedirect("/orders/{$order2->id}");
        $this->assertSame('preparando', $order2->fresh()->estado->value);
    }
}
