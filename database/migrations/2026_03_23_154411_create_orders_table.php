<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->string('source_reference')->nullable();
            $table->string('cliente_nombre');
            $table->string('rut_cliente')->nullable();
            $table->string('tipo_entrega');
            $table->string('estado');
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por')->constrained('users');
            $table->foreignId('liberado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_liberacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
