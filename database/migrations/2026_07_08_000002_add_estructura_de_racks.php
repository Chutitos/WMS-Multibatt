<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estructura física de los racks: cada rack tiene columnas x niveles
     * y en cada puesto (columna, nivel) se deposita UN pallet con UN solo
     * tipo de batería. columna/nivel en product_locations son nullable
     * para no romper las existencias ya registradas sin puesto.
     */
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->unsignedInteger('columnas')->default(3)->after('height');
            $table->unsignedInteger('niveles')->default(3)->after('columnas');
        });

        Schema::table('product_locations', function (Blueprint $table) {
            $table->unsignedInteger('columna')->nullable()->after('warehouse_location_id');
            $table->unsignedInteger('nivel')->nullable()->after('columna');
        });
    }

    public function down(): void
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropColumn(['columna', 'nivel']);
        });

        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn(['columnas', 'niveles']);
        });
    }
};
