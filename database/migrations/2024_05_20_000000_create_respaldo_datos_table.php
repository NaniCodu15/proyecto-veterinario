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
        Schema::create('respaldo_datos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_respaldo');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->string('estado', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respaldo_datos');
    }
};
