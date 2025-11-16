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
        // Tabla para almacenar entradas de caché de aplicación.
        Schema::create('cache', function (Blueprint $table) {
            // Clave única de la entrada de caché.
            $table->string('key')->primary();
            // Valor serializado almacenado temporalmente.
            $table->mediumText('value');
            // Momento de expiración de la entrada.
            $table->integer('expiration');
        });

        // Tabla que gestiona bloqueos de caché para operaciones atómicas.
        Schema::create('cache_locks', function (Blueprint $table) {
            // Clave única del bloqueo.
            $table->string('key')->primary();
            // Identificador del proceso que tomó el bloqueo.
            $table->string('owner');
            // Tiempo límite de expiración del bloqueo.
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
