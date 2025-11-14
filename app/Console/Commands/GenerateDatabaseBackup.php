<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

class GenerateDatabaseBackup extends Command
{
    protected $signature = 'backup:generate';

    protected $description = 'Genera una copia de seguridad de la base de datos y la sube a Google Drive.';

    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Iniciando la generación de la copia de seguridad...');

        try {
            $resultado = $backupService->createBackup();
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error('Ocurrió un error inesperado al generar la copia de seguridad.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $respaldo = $resultado['respaldo'];
        $estado = $resultado['estado'];
        $mensaje = $resultado['mensaje'];

        if ($estado === 'Correcto') {
            $this->info($mensaje);
            $this->line(sprintf('ID respaldo: %s', $respaldo->id));
            $this->line(sprintf('Archivo local: %s', $respaldo->ruta_archivo));

            if ($respaldo->ruta_remota) {
                $this->line(sprintf('Archivo en Drive: %s', $respaldo->ruta_remota));
            }

            return self::SUCCESS;
        }

        $this->error($mensaje);

        return self::FAILURE;
    }
}
