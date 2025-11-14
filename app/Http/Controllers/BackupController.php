<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use App\Services\DatabaseBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(private readonly DatabaseBackupService $backupService)
    {
    }

    /**
     * Genera una nueva copia de seguridad completa de la base de datos.
     */
    public function generate(): JsonResponse
    {
        try {
            $resultado = $this->backupService->createBackup();
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Error inesperado al generar la copia de seguridad', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error inesperado al generar la copia de seguridad.',
            ], 500);
        }

        $respaldo = $resultado['respaldo'];
        $estado = $resultado['estado'];
        $mensaje = $resultado['mensaje'];

        return response()->json([
            'message' => $mensaje,
            'respaldo' => $this->formatRespaldo($respaldo),
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
            ->map(fn (RespaldoDato $respaldo) => $this->formatRespaldo($respaldo));

        return response()->json([
            'data' => $respaldos,
        ]);
    }

    public function download(RespaldoDato $respaldo): BinaryFileResponse|StreamedResponse
    {
        $localPath = $this->backupService->resolveLocalPath($respaldo);

        if ($localPath && File::exists($localPath)) {
            $mime = File::mimeType($localPath) ?: 'application/octet-stream';

            return response()->download($localPath, $respaldo->nombre_archivo, [
                'Content-Type' => $mime,
            ]);
        }

        $remotePath = $this->backupService->resolveRemotePath($respaldo);

        if ($remotePath) {
            try {
                $disk = Storage::disk('google');
            } catch (Throwable $exception) {
                Log::error('No se pudo acceder al disco de Google Drive para la descarga.', [
                    'exception' => $exception->getMessage(),
                ]);

                abort(500, 'No se pudo acceder al servicio de almacenamiento en la nube.');
            }

            if ($disk->exists($remotePath)) {
                $stream = $disk->readStream($remotePath);

                if ($stream === false) {
                    abort(500, 'No se pudo acceder al archivo en Google Drive.');
                }

                $mime = 'application/octet-stream';

                try {
                    $mimeType = $disk->mimeType($remotePath);

                    if (is_string($mimeType) && $mimeType !== '') {
                        $mime = $mimeType;
                    }
                } catch (Throwable $exception) {
                    Log::warning('No se pudo determinar el tipo MIME del archivo de Drive.', [
                        'ruta' => $remotePath,
                        'exception' => $exception->getMessage(),
                    ]);
                }

                return response()->streamDownload(function () use ($stream) {
                    fpassthru($stream);

                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }, $respaldo->nombre_archivo, [
                    'Content-Type' => $mime,
                ]);
            }
        }

        abort(404, 'No se encontró el archivo de respaldo solicitado.');
    }

    public function restore(RespaldoDato $respaldo): JsonResponse
    {
        Log::info('Solicitud de restauración recibida para un respaldo.', [
            'respaldo_id' => $respaldo->id,
        ]);

        return response()->json([
            'message' => 'La solicitud de restauración fue recibida. Proceda con el proceso de restauración de forma manual.',
        ]);
    }

    private function formatRespaldo(RespaldoDato $respaldo): array
    {
        return [
            'id' => $respaldo->id,
            'id_respaldo' => $respaldo->id,
            'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
            'nombre_archivo' => $respaldo->nombre_archivo,
            'ruta_archivo' => $respaldo->ruta_archivo,
            'ruta_remota' => $respaldo->ruta_remota,
            'estado' => $respaldo->estado,
        ];
    }
}
