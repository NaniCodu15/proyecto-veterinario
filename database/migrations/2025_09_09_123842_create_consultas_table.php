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
        // Tabla que registra cada consulta realizada dentro de una historia clínica.
        Schema::create('consultas', function (Blueprint $table) {
            // Identificador de la consulta.
            $table->id('id_consulta');
            // Relación con la historia clínica correspondiente.
            $table->unsignedBigInteger('id_historia');
            // Fecha y hora en que se efectuó la consulta.
            $table->timestamp('fecha_consulta')->useCurrent();
            // Síntomas reportados por el propietario o detectados.
            $table->text('sintomas')->nullable();
            // Diagnóstico realizado durante la consulta.
            $table->text('diagnostico')->nullable();
            // Tratamiento sugerido o aplicado en texto libre.
            $table->text('tratamiento')->nullable();
            // Observaciones adicionales del profesional.
            $table->text('observaciones')->nullable();

            $table->foreign('id_historia')->references('id_historia')->on('historia_clinicas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
