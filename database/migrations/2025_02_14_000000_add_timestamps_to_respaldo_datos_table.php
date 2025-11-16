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
        // Se añaden columnas de auditoría si aún no existen para registrar creación y actualización.
        Schema::table('respaldo_datos', function (Blueprint $table) {
            if (!Schema::hasColumn('respaldo_datos', 'created_at')) {
                // Marca temporal de creación del respaldo.
                $table->timestamp('created_at')->nullable()->after('estado');
            }

            if (!Schema::hasColumn('respaldo_datos', 'updated_at')) {
                // Marca temporal de última actualización del registro de respaldo.
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respaldo_datos', function (Blueprint $table) {
            if (Schema::hasColumn('respaldo_datos', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            if (Schema::hasColumn('respaldo_datos', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
