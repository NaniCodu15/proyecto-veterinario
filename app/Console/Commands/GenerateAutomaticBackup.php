<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class GenerateAutomaticBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:generate-automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera automáticamente una copia de seguridad de la base de datos.';

    public function __construct(private readonly BackupService $backupService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = $this->backupService->generate();
        $payload = $result['data'] ?? [];
        $status = $result['status'] ?? 500;
        $message = $payload['message'] ?? 'Proceso finalizado.';

        if ($status >= 400) {
            $this->error($message);

            if (isset($payload['respaldo'])) {
                $this->table(['ID', 'Archivo', 'Estado'], [[
                    $payload['respaldo']['id_respaldo'] ?? $payload['respaldo']['id'] ?? 'N/A',
                    $payload['respaldo']['nombre_archivo'] ?? 'N/A',
                    $payload['respaldo']['estado'] ?? 'Fallido',
                ]]);
            }

            return self::FAILURE;
        }

        $this->info($message);

        if (isset($payload['respaldo'])) {
            $this->line(sprintf(
                'Respaldo #%s almacenado en %s.',
                $payload['respaldo']['id_respaldo'] ?? $payload['respaldo']['id'] ?? 'N/A',
                $payload['respaldo']['ruta_archivo'] ?? 'N/D'
            ));
        }

        return self::SUCCESS;
    }
}
