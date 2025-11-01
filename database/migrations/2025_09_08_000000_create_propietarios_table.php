<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id('id_propietario');
            $table->string('dni', 15)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps(); // 🔹 agrega esto si usarás created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propietarios');
    }
};
