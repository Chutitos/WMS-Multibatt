<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * users.role_id nació con cascadeOnDelete() contra una tabla catálogo
     * de 3 filas: borrar un rol borraría en cascada a todos sus usuarios.
     * Debe fallar con un error de FK en vez de borrar datos.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->references('id')->on('roles')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });
    }
};
