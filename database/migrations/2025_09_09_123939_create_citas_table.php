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
        // Tabla que almacena las citas programadas relacionadas con historias clínicas.
        Schema::create('citas', function (Blueprint $table) {
            // Identificador principal de la cita.
            $table->id('id_cita');
            // Historia clínica a la cual pertenece la cita.
            $table->unsignedBigInteger('id_historia');
            // Fecha en la que se atenderá la cita.
            $table->date('fecha_cita');
            // Hora programada para la atención.
            $table->time('hora_cita');
            // Motivo principal de la cita.
            $table->string('motivo', 255)->nullable();
            // Estado de la cita (pendiente, atendida o cancelada).
            $table->enum('estado', ['Pendiente','Atendida','Cancelada'])->default('Pendiente');

            $table->foreign('id_historia')->references('id_historia')->on('historia_clinicas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
