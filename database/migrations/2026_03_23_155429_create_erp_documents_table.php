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
        Schema::create('erp_documents', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('cliente_nombre');
            $table->string('rut_cliente')->nullable();
            $table->date('fecha_documento');
            $table->string('estado_sync')->default('pending');
            $table->json('payload_json')->nullable();
            $table->foreignId('imported_by')->constrained('users');
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_documents');
    }
};
