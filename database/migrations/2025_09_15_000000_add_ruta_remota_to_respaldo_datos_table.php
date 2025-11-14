<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respaldo_datos', function (Blueprint $table) {
            if (!Schema::hasColumn('respaldo_datos', 'ruta_remota')) {
                $table->string('ruta_remota')->nullable()->after('ruta_archivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('respaldo_datos', function (Blueprint $table) {
            if (Schema::hasColumn('respaldo_datos', 'ruta_remota')) {
                $table->dropColumn('ruta_remota');
            }
        });
    }
};
