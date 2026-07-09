<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ficha técnica de batería: lo que identifica una batería en bodega
     * (marca, tipo de vehículo, voltaje, capacidad) más dos reglas de
     * operación: cada cuántos meses almacenada necesita recarga y el
     * mínimo físico deseado antes de alertar.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('marca', 100)->nullable()->after('name');
            $table->string('tipo', 50)->nullable()->after('marca');
            $table->string('voltaje', 20)->nullable()->after('tipo');
            $table->unsignedInteger('capacidad_ah')->nullable()->after('voltaje');
            $table->unsignedInteger('meses_recarga')->default(6)->after('capacidad_ah');
            $table->unsignedInteger('stock_minimo')->default(0)->after('meses_recarga');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['marca', 'tipo', 'voltaje', 'capacidad_ah', 'meses_recarga', 'stock_minimo']);
        });
    }
};
