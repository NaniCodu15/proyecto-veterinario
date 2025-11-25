<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla que almacena las historias clínicas asociadas a cada mascota.
        Schema::create('historia_clinicas', function (Blueprint $table) {
            // Identificador principal de la historia clínica.
            $table->id('id_historia');
            // Relación con la mascota propietaria de la historia.
            $table->unsignedBigInteger('id_mascota');
            // Código único que identifica la historia clínica.
            $table->string('numero_historia')->unique();
            // Fecha en la que se abrió el expediente clínico.
            $table->date('fecha_apertura');
            // Peso del animal registrado al momento de la apertura.
            $table->decimal('peso', 5, 2)->nullable();
            // Temperatura corporal registrada, opcional.
            $table->decimal('temperatura', 4, 1)->nullable();
            // Frecuencia cardiaca observada durante la evaluación.
            $table->string('frecuencia_cardiaca')->nullable();
            // Descripción de síntomas reportados.
            $table->text('sintomas')->nullable();
            // Diagnóstico inicial o actual.
            $table->text('diagnostico')->nullable();
            // Tratamientos generales aplicados (texto libre).
            $table->text('tratamientos')->nullable();
            // Notas adicionales del veterinario.
            $table->text('notas')->nullable();
            // Ruta del archivo adjunto con documentos de soporte.
            $table->string('archivo')->nullable(); // path upload
            // Identificador del usuario que creó el registro.
            $table->unsignedBigInteger('created_by')->nullable();
            // Marcas de tiempo de creación y actualización.
            $table->timestamps();

            $table->foreign('id_mascota')->references('id_mascota')->on('mascotas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_clinicas');
    }
};
