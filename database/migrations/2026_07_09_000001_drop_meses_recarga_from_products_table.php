<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La alerta de recarga por meses almacenados se descartó: el control
     * de stock (y su antigüedad contable) vendrá del ERP Defontana, así
     * que no corresponde construir esa regla sobre la capa física local.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('meses_recarga');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('meses_recarga')->default(6)->after('capacidad_ah');
        });
    }
};
