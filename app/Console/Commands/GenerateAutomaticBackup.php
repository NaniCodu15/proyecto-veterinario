<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Throwable;

class GenerateAutomaticBackup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:auto';

    /**
     * The console command description.
     */
    protected $description = 'Genera una copia de seguridad automática y la almacena en Google Drive';

    public function __construct(private readonly BackupService $backupService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando generación automática de respaldo...');

        try {
            $resultado = $this->backupService->handleBackup();
        } catch (Throwable $exception) {
            $this->error('Ocurrió un error inesperado al generar el respaldo automático.');

            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $respaldo = $resultado['respaldo'];

        if (($resultado['estado'] ?? '') !== 'Correcto') {
            $this->error($resultado['mensaje'] ?? 'El respaldo automático falló.');

            if ($respaldo && $respaldo->nombre_archivo) {
                $this->error(sprintf('Archivo generado: %s', $respaldo->nombre_archivo));
            }

            return self::FAILURE;
        }

        $this->info($resultado['mensaje'] ?? 'Respaldo automático generado correctamente.');

        if ($respaldo && $respaldo->id) {
            $this->info(sprintf('ID de respaldo: %s', $respaldo->id));
        }

        return self::SUCCESS;
    }
}
