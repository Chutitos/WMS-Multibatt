<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('retirado_por_nombre')->nullable();
            $table->string('retirado_por_rut')->nullable();
            $table->string('transportista')->nullable();
            $table->string('guia_despacho')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['retirado_por_nombre', 'retirado_por_rut', 'transportista', 'guia_despacho']);
        });
    }
};
