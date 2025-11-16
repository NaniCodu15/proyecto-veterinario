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
        // Tabla que almacena los tratamientos prescritos en cada consulta.
        Schema::create('tratamientos', function (Blueprint $table) {
            // Identificador del tratamiento.
            $table->id('id_tratamiento');
            // Relación con la consulta en la que se indicó el tratamiento.
            $table->unsignedBigInteger('id_consulta');
            // Nombre del medicamento recomendado.
            $table->string('medicamento', 150)->nullable();
            // Dosis sugerida del medicamento.
            $table->string('dosis', 100)->nullable();
            // Duración del tratamiento indicado.
            $table->string('duracion', 100)->nullable();
            // Indicaciones adicionales o instrucciones de uso.
            $table->text('indicaciones')->nullable();

            $table->foreign('id_consulta')->references('id_consulta')->on('consultas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
