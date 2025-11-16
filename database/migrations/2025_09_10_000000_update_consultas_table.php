<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Se añaden campos adicionales para enriquecer la información clínica de las consultas.
        Schema::table('consultas', function (Blueprint $table) {
            if (!Schema::hasColumn('consultas', 'motivo')) {
                // Motivo textual de la consulta.
                $table->string('motivo')->nullable()->after('fecha_consulta');
            }

            if (!Schema::hasColumn('consultas', 'peso')) {
                // Peso registrado durante la consulta para seguimiento.
                $table->decimal('peso', 5, 2)->nullable()->after('motivo');
            }

            if (!Schema::hasColumn('consultas', 'temperatura')) {
                // Temperatura tomada durante la atención.
                $table->decimal('temperatura', 4, 1)->nullable()->after('peso');
            }

            if (!Schema::hasColumn('consultas', 'created_at')) {
                // Marcas de tiempo de creación y actualización si no existían previamente.
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            if (Schema::hasColumn('consultas', 'motivo')) {
                $table->dropColumn('motivo');
            }

            if (Schema::hasColumn('consultas', 'peso')) {
                $table->dropColumn('peso');
            }

            if (Schema::hasColumn('consultas', 'temperatura')) {
                $table->dropColumn('temperatura');
            }

            if (Schema::hasColumn('consultas', 'created_at') && Schema::hasColumn('consultas', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
