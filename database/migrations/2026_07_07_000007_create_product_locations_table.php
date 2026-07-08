<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Existencia física de un producto en una ubicación de la bodega.
     * Es la capa que le falta a Defontana (que solo sabe stock contable
     * total, no en qué punto físico de la bodega está cada lote) y es
     * la que permite calcular FIFO real por fecha_ingreso.
     */
    public function up(): void
    {
        Schema::create('product_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_location_id')->constrained()->cascadeOnDelete();
            $table->string('lote')->nullable();
            $table->date('fecha_ingreso');
            $table->integer('cantidad');
            $table->timestamps();

            $table->index(['product_id', 'fecha_ingreso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_locations');
    }
};
