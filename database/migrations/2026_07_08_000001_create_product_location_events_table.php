<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trazabilidad de existencias: quién creó, editó o eliminó cada
     * registro de product_locations y qué cambió. El evento sobrevive
     * aunque la existencia se elimine (FK nullable con nullOnDelete),
     * por eso el detalle guarda el snapshot en texto.
     */
    public function up(): void
    {
        Schema::create('product_location_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accion'); // creada | editada | eliminada
            $table->text('detalle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_location_events');
    }
};
