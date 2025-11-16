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
        // Tabla que registra las vacunas aplicadas a las mascotas.
        Schema::create('vacunas', function (Blueprint $table) {
             $table->id('id_vacuna');
            // Mascota a la que se aplicó la vacuna.
            $table->unsignedBigInteger('id_mascota');
            // Nombre comercial o tipo de vacuna.
            $table->string('nombre_vacuna', 150);
            // Fecha en que se aplicó la dosis.
            $table->date('fecha_aplicacion');
            // Fecha prevista para la próxima aplicación, si existe.
            $table->date('fecha_proxima')->nullable();
            // Observaciones relevantes sobre la vacunación.
            $table->text('observaciones')->nullable();

            $table->foreign('id_mascota')->references('id_mascota')->on('mascotas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacunas');
    }
};
