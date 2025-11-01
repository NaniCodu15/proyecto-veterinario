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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id('id_mascota');
            $table->string('nombre', 100);
            $table->string('especie', 50);
            $table->string('raza', 100)->nullable();
            $table->enum('sexo', ['Macho', 'Hembra']);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('color', 50)->nullable();
            $table->unsignedBigInteger('propietario_id');
            $table->timestamp('fecha_registro')->useCurrent();
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
