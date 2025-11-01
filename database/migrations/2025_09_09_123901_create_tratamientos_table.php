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
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id('id_tratamiento');
            $table->unsignedBigInteger('id_consulta');
            $table->string('medicamento', 150)->nullable();
            $table->string('dosis', 100)->nullable();
            $table->string('duracion', 100)->nullable();
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
