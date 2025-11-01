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
        Schema::create('vacunas', function (Blueprint $table) {
             $table->id('id_vacuna');
            $table->unsignedBigInteger('id_mascota');
            $table->string('nombre_vacuna', 150);
            $table->date('fecha_aplicacion');
            $table->date('fecha_proxima')->nullable();
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
