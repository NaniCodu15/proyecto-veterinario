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
        // Tabla que almacena los trabajos en cola pendientes.
        Schema::create('jobs', function (Blueprint $table) {
            // Identificador del trabajo.
            $table->id();
            // Nombre de la cola a la que pertenece para segmentar procesos.
            $table->string('queue')->index();
            // Carga útil serializada del trabajo.
            $table->longText('payload');
            // Número de intentos realizados para procesarlo.
            $table->unsignedTinyInteger('attempts');
            // Momento en que fue reservado por un worker.
            $table->unsignedInteger('reserved_at')->nullable();
            // Momento a partir del cual el trabajo está disponible para ser procesado.
            $table->unsignedInteger('available_at');
            // Fecha de creación del trabajo.
            $table->unsignedInteger('created_at');
        });

        // Tabla que agrupa lotes de trabajos para seguimiento conjunto.
        Schema::create('job_batches', function (Blueprint $table) {
            // Identificador del lote.
            $table->string('id')->primary();
            // Nombre descriptivo del batch.
            $table->string('name');
            // Total de trabajos que conforman el lote.
            $table->integer('total_jobs');
            // Cantidad de trabajos aún pendientes.
            $table->integer('pending_jobs');
            // Cantidad de trabajos que fallaron.
            $table->integer('failed_jobs');
            // Identificadores de los trabajos fallidos para trazabilidad.
            $table->longText('failed_job_ids');
            // Opciones adicionales serializadas (por ejemplo, callbacks).
            $table->mediumText('options')->nullable();
            // Marca temporal de cancelación del lote.
            $table->integer('cancelled_at')->nullable();
            // Fecha de creación del batch.
            $table->integer('created_at');
            // Fecha de finalización del batch.
            $table->integer('finished_at')->nullable();
        });

        // Tabla que guarda registros de trabajos fallidos para auditoría.
        Schema::create('failed_jobs', function (Blueprint $table) {
            // Identificador autoincremental del fallo.
            $table->id();
            // UUID para identificar el trabajo fallido de forma única.
            $table->string('uuid')->unique();
            // Conexión de cola utilizada.
            $table->text('connection');
            // Cola en la que se intentó procesar el trabajo.
            $table->text('queue');
            // Datos serializados del trabajo que falló.
            $table->longText('payload');
            // Excepción detallada ocurrida durante el procesamiento.
            $table->longText('exception');
            // Fecha y hora en la que se registró el fallo.
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
