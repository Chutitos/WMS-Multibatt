<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * pos_x/pos_y/width/height guardan la posición del bloque en el
     * editor visual de la bodega (estilo draw.io simplificado), no
     * coordenadas físicas reales.
     */
    public function up(): void
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->integer('pos_x')->default(0);
            $table->integer('pos_y')->default(0);
            $table->integer('width')->default(120);
            $table->integer('height')->default(80);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
