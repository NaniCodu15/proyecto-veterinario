<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function store(): RedirectResponse
    {
        $now = now();
        $fileName = 'backup_' . $now->format('Y_m_d_His') . '.sql';
        $relativePath = 'storage/backups/' . $fileName;
        $backupDirectory = storage_path('backups');
        $absolutePath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;

        $status = 'Correcto';
        $message = 'La copia de seguridad se generó correctamente.';

        try {
            $connectionName = Config::get('database.default');
            $config = Config::get("database.connections.{$connectionName}");

            if (! $config || ($config['driver'] ?? null) !== 'mysql') {
                throw new \RuntimeException('La conexión configurada no es compatible con respaldos automáticos.');
            }

            if (! File::isDirectory($backupDirectory)) {
                File::makeDirectory($backupDirectory, 0755, true);
            }

            $command = [
                'mysqldump',
                '--host=' . ($config['host'] ?? '127.0.0.1'),
                '--port=' . ($config['port'] ?? 3306),
                '--user=' . ($config['username'] ?? 'root'),
                '--routines',
                '--events',
                '--single-transaction',
                '--quick',
                '--lock-tables=false',
                '--result-file=' . $absolutePath,
                $config['database'] ?? 'hospital_veterinario',
            ];

            if (! empty($config['password'])) {
                // Insert the password option before the database name
                array_splice($command, 4, 0, '--password=' . $config['password']);
            }

            $process = new Process($command);
            $process->setTimeout(300);
            $process->run();

            if (! $process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput() ?: 'No fue posible generar el respaldo.');
            }

            if (! File::exists($absolutePath) || File::size($absolutePath) === 0) {
                throw new \RuntimeException('El archivo de respaldo no se generó correctamente.');
            }
        } catch (\Throwable $exception) {
            $status = 'Fallido';
            $message = 'No se pudo generar la copia de seguridad. Por favor, inténtalo nuevamente.';
            Log::error('Error al generar respaldo de la base de datos', [
                'error' => $exception->getMessage(),
            ]);

            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        RespaldoDato::create([
            'fecha_respaldo' => $now,
            'nombre_archivo' => $fileName,
            'ruta_archivo' => $relativePath,
            'estado' => $status,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('backup_message', $message)
            ->with('backup_status', $status);
    }
}

