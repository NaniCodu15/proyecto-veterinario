<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historia_clinicas', function (Blueprint $table) {
            $table->id('id_historia');
            $table->unsignedBigInteger('id_mascota');
            $table->string('numero_historia')->unique();
            $table->date('fecha_apertura');
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->string('frecuencia_cardiaca')->nullable();
            $table->text('sintomas')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamientos')->nullable();
            $table->text('vacunas')->nullable();
            $table->text('notas')->nullable();
            $table->string('archivo')->nullable(); // path upload
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('id_mascota')->references('id_mascota')->on('mascotas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_clinicas');
    }
};
