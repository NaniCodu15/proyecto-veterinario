<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /**
     * Genera una nueva copia de seguridad completa de la base de datos.
     */
    public function generate(): JsonResponse
    {
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (!is_array($connection) || ($connection['driver'] ?? null) !== 'mysql') {
            return response()->json([
                'message' => 'La copia de seguridad solo está disponible para conexiones MySQL.',
            ], 422);
        }

        $timestamp = now();
        $fileName = 'backup_' . $timestamp->format('Y_m_d_His') . '.sql';
        $storageDirectory = storage_path('backups');
        $fullPath = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;
        $relativePath = 'storage/backups/' . $fileName;

        if (!File::isDirectory($storageDirectory)) {
            File::makeDirectory($storageDirectory, 0755, true);
        }

        $estado = 'Correcto';
        $mensaje = 'La copia de seguridad se generó correctamente.';

        try {
            $command = [
                'mysqldump',
                '--no-tablespaces',
                '-h', $connection['host'] ?? '127.0.0.1',
                '-P', (string) ($connection['port'] ?? 3306),
                '-u', $connection['username'],
                $connection['database'],
            ];

            $process = new Process($command);
            $env = $process->getEnv() ?? [];

            if (!empty($connection['password'])) {
                $env['MYSQL_PWD'] = $connection['password'];
            }

            $process->setEnv($env);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'No se pudo generar la copia de seguridad.');
            }

            $output = $process->getOutput();
            File::put($fullPath, $output);
        } catch (\Throwable $exception) {
            $estado = 'Fallido';
            $mensaje = 'No se pudo generar la copia de seguridad.';
            Log::error('Error al generar la copia de seguridad', [
                'exception' => $exception->getMessage(),
            ]);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        $respaldo = RespaldoDato::create([
            'fecha_respaldo' => $timestamp,
            'nombre_archivo' => $fileName,
            'ruta_archivo' => $relativePath,
            'estado' => $estado,
        ]);

        return response()->json([
            'message' => $mensaje,
            'respaldo' => [
                'id' => $respaldo->id,
                'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
                'nombre_archivo' => $respaldo->nombre_archivo,
                'ruta_archivo' => $respaldo->ruta_archivo,
                'estado' => $respaldo->estado,
            ],
        ], $estado === 'Correcto' ? 201 : 500);
    }

    /**
     * Devuelve el listado de copias de seguridad registradas.
     */
    public function index(): JsonResponse
    {
        $respaldos = RespaldoDato::query()
            ->orderByDesc('fecha_respaldo')
            ->get()
            ->map(fn (RespaldoDato $respaldo) => [
                'id' => $respaldo->id,
                'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
                'nombre_archivo' => $respaldo->nombre_archivo,
                'ruta_archivo' => $respaldo->ruta_archivo,
                'estado' => $respaldo->estado,
            ]);

        return response()->json([
            'data' => $respaldos,
        ]);
    }
}
