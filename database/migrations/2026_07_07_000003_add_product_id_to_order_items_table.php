<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * product_id nace nullable a propósito: las órdenes existentes no tienen
     * catálogo detrás, y producto_codigo/producto_nombre se mantienen como
     * snapshot histórico aunque el producto exista y cambie de nombre después.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
