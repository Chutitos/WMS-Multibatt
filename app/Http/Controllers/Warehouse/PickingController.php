<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\OrderItemPick;
use App\Models\Product;
use App\Models\ProductLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PickingController extends Controller
{
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->estado !== OrderStatus::PREPARANDO) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'El picking solo está disponible mientras la orden está en preparación.');
        }

        $order->load('items.product');

        $sugerencias = [];

        foreach ($order->items as $item) {
            if ($item->cantidad_confirmada >= $item->cantidad_solicitada || ! $item->product_id) {
                continue;
            }

            $sugerencias[$item->id] = ProductLocation::disponibleFifo($item->product_id)
                ->with('warehouseLocation')
                ->first();
        }

        return view('orders.picking', compact('order', 'sugerencias'));
    }

    public function escanear(Request $request, Order $order)
    {
        if ($order->estado !== OrderStatus::PREPARANDO) {
            return response()->json(['message' => 'La orden no está en preparación.'], 422);
        }

        $validated = $request->validate([
            'codigo' => 'required|string',
        ]);

        $product = Product::where('barcode', $validated['codigo'])
            ->orWhere('sku', $validated['codigo'])
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Código no reconocido.'], 422);
        }

        $itemExiste = $order->items()->where('product_id', $product->id)->exists();

        if (! $itemExiste) {
            return response()->json(['message' => "\"{$product->name}\" no corresponde a esta orden."], 422);
        }

        // Todo el chequeo + mutación va bloqueado dentro de la misma
        // transacción: sin esto, dos escaneos simultáneos del mismo producto
        // podrían leer la misma existencia disponible antes de que ninguno
        // la descuente, dejando la cantidad en negativo o sobre-confirmando
        // el item más allá de lo solicitado.
        try {
            $respuesta = DB::transaction(function () use ($order, $product, $request) {
                $item = OrderItem::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if ($item->cantidad_confirmada >= $item->cantidad_solicitada) {
                    throw ValidationException::withMessages([
                        'codigo' => 'Ya se confirmó la cantidad completa de este producto.',
                    ]);
                }

                $location = ProductLocation::disponibleFifo($product->id)
                    ->lockForUpdate()
                    ->with('warehouseLocation')
                    ->first();

                if (! $location) {
                    throw ValidationException::withMessages([
                        'codigo' => 'No hay existencia registrada para este producto en ninguna ubicación.',
                    ]);
                }

                $item->increment('cantidad_confirmada');
                $location->decrement('cantidad');

                // Línea de picking: registra de qué pallet salió la unidad
                // para poder devolverla exactamente ahí si se cancela.
                $pick = OrderItemPick::firstOrCreate([
                    'order_item_id' => $item->id,
                    'product_location_id' => $location->id,
                ], [
                    'warehouse_location_id' => $location->warehouse_location_id,
                    'cantidad' => 0,
                ]);
                $pick->increment('cantidad');

                OrderEvent::create([
                    'order_id' => $order->id,
                    'tipo_evento' => 'escaneo',
                    'descripcion' => "Escaneado: {$item->producto_nombre} desde {$location->warehouseLocation->codigo}.",
                    'user_id' => $request->user()->id,
                ]);

                $completo = $item->cantidad_confirmada >= $item->cantidad_solicitada;

                // Recalcular FIFO después de descontar: si este estante quedó
                // vacío, la siguiente unidad puede estar en otro, y el operario
                // debe saberlo sin recargar la pantalla.
                $siguiente = $completo
                    ? null
                    : ProductLocation::disponibleFifo($product->id)->with('warehouseLocation')->first();

                $puestoOrigen = $location->puesto() ? " ({$location->puesto()})" : '';
                $mensaje = "{$item->producto_nombre} confirmado ({$item->cantidad_confirmada} de {$item->cantidad_solicitada}) — tomado de {$location->warehouseLocation->nombre}{$puestoOrigen}.";

                if ($siguiente && $siguiente->id !== $location->id) {
                    $puestoSiguiente = $siguiente->puesto() ? ", {$siguiente->puesto()}" : '';
                    $mensaje .= " Siguiente unidad en {$siguiente->warehouseLocation->nombre} ({$siguiente->warehouseLocation->codigo}{$puestoSiguiente}).";
                }

                return [
                    'message' => $mensaje,
                    'item_id' => $item->id,
                    'cantidad_confirmada' => $item->cantidad_confirmada,
                    'cantidad_solicitada' => $item->cantidad_solicitada,
                    'completo' => $completo,
                    'siguiente_ubicacion' => $siguiente ? [
                        'id' => $siguiente->warehouse_location_id,
                        'nombre' => $siguiente->warehouseLocation->nombre,
                        'codigo' => $siguiente->warehouseLocation->codigo,
                        'puesto' => $siguiente->puesto(),
                    ] : null,
                ];
            });
        } catch (ValidationException $e) {
            return response()->json(['message' => collect($e->errors())->flatten()->first()], 422);
        }

        return response()->json($respuesta);
    }
}
