<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla que almacena los datos de los propietarios de mascotas.
        Schema::create('propietarios', function (Blueprint $table) {
            // Identificador único del propietario.
            $table->id('id_propietario');
            // Documento de identidad único para evitar duplicados.
            $table->string('dni', 15)->unique();
            // Nombres del propietario, campo requerido.
            $table->string('nombres', 100);
            // Apellidos del propietario, campo requerido.
            $table->string('apellidos', 100);
            // Teléfono de contacto opcional.
            $table->string('telefono', 20)->nullable();
            // Dirección de residencia opcional.
            $table->string('direccion', 200)->nullable();
            // Fecha de registro predeterminada a la hora actual.
            $table->timestamp('fecha_registro')->useCurrent();
            // Marcas de tiempo para auditoría de creación y actualización.
            $table->timestamps(); // 🔹 agrega esto si usarás created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propietarios');
    }
};
