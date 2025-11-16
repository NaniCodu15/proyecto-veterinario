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
        // Tabla que almacena la información básica de las mascotas.
        Schema::create('mascotas', function (Blueprint $table) {
            // Identificador principal de la mascota.
            $table->id('id_mascota');
            // Nombre asignado por el propietario.
            $table->string('nombre', 100);
            // Especie de la mascota (perro, gato, etc.).
            $table->string('especie', 50);
            // Raza opcional de la mascota.
            $table->string('raza', 100)->nullable();
            // Sexo de la mascota restringido a Macho o Hembra.
            $table->enum('sexo', ['Macho', 'Hembra']);
            // Fecha aproximada de nacimiento.
            $table->date('fecha_nacimiento')->nullable();
            // Color del pelaje o características principales.
            $table->string('color', 50)->nullable();
            // Relación con el propietario responsable.
            $table->unsignedBigInteger('propietario_id');
            // Fecha de registro inicial de la mascota.
            $table->timestamp('fecha_registro')->useCurrent();
            // Timestamps de creación y actualización.
            $table->timestamps();

            // Relación con la tabla propietarios
            $table->foreign('propietario_id')->references('id_propietario')->on('propietarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
