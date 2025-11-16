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
        // Tabla principal de usuarios autenticables: almacena credenciales y metadatos de sesión.
        Schema::create('users', function (Blueprint $table) {
            // Identificador autoincremental principal.
            $table->id();
            // Nombre visible del usuario dentro del sistema.
            $table->string('name');
            // Correo único para inicio de sesión y recuperación de contraseña.
            $table->string('email')->unique();
            // Marca temporal opcional cuando el correo ha sido verificado.
            $table->timestamp('email_verified_at')->nullable();
            // Contraseña cifrada del usuario.
            $table->string('password');
            // Token persistente para la funcionalidad "recordarme".
            $table->rememberToken();
            // Fechas de creación y actualización del registro.
            $table->timestamps();
        });

        // Tabla para solicitudes de restablecimiento de contraseña con token temporal.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Correo electrónico asociado al token, actúa como clave primaria.
            $table->string('email')->primary();
            // Token generado para validar la recuperación.
            $table->string('token');
            // Fecha en la que se creó el token para poder depurar expiraciones.
            $table->timestamp('created_at')->nullable();
        });

        // Tabla de sesiones que almacena el estado de autenticación de los usuarios.
        Schema::create('sessions', function (Blueprint $table) {
            // Identificador de sesión generado por Laravel.
            $table->string('id')->primary();
            // Relación opcional con el usuario autenticado.
            $table->foreignId('user_id')->nullable()->index();
            // Dirección IP desde donde se inició la sesión.
            $table->string('ip_address', 45)->nullable();
            // Cadena del agente de usuario para auditoría.
            $table->text('user_agent')->nullable();
            // Datos serializados de la sesión.
            $table->longText('payload');
            // Marca de tiempo de la última actividad para depuración y limpieza.
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
