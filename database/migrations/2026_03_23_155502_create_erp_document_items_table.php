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
        Schema::create('erp_document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erp_document_id')->constrained()->cascadeOnDelete();
            $table->string('producto_codigo');
            $table->string('producto_nombre');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_document_items');
    }
};
