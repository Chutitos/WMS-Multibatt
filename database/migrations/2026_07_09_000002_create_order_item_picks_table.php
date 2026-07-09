<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Líneas de picking: de qué pallet exacto salió cada unidad escaneada.
     * Sin esto, cancelar una orden a medio preparar pierde el stock físico
     * para siempre (no habría cómo saber a qué estante devolverlo).
     * warehouse_location_id es respaldo por si el pallet original se elimina.
     */
    public function up(): void
    {
        Schema::create('order_item_picks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_location_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('cantidad');
            $table->timestamps();

            $table->unique(['order_item_id', 'product_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_picks');
    }
};
