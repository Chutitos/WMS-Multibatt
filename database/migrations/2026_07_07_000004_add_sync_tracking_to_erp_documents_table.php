<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * unique() en external_id es la pieza mínima de idempotencia antes de
     * conectar la API real: evita duplicar un documento si el import se
     * reintenta. attempts/last_error/last_attempt_at permiten ver por qué
     * falló una sincronización sin revisar logs de archivo a mano.
     */
    public function up(): void
    {
        Schema::table('erp_documents', function (Blueprint $table) {
            $table->unique('external_id');
            $table->unsignedInteger('attempts')->default(0)->after('estado_sync');
            $table->text('last_error')->nullable()->after('attempts');
            $table->timestamp('last_attempt_at')->nullable()->after('last_error');
        });
    }

    public function down(): void
    {
        Schema::table('erp_documents', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->dropColumn(['attempts', 'last_error', 'last_attempt_at']);
        });
    }
};
