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
        // Tabla que almacena el historial de copias de seguridad generadas.
        Schema::create('respaldo_datos', function (Blueprint $table) {
            // Identificador del respaldo.
            $table->id();
            // Fecha y hora en que se creó el respaldo.
            $table->timestamp('fecha_respaldo');
            // Nombre del archivo generado para el backup.
            $table->string('nombre_archivo');
            // Ruta relativa o absoluta donde se encuentra almacenado el archivo.
            $table->string('ruta_archivo');
            // Estado del respaldo (por ejemplo, completado o fallido).
            $table->string('estado', 30);
            // Timestamps de creación y actualización del registro para auditoría.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respaldo_datos');
    }
};
