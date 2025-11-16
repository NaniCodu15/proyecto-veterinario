<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;

class BackupController extends Controller
{
    /**
     * Inyecta el servicio encargado de generar y limpiar respaldos.
     *
     * @param BackupService $backupService Servicio que ejecuta las operaciones de backup.
     */
    public function __construct(private readonly BackupService $backupService)
    {
    }

    /**
     * Genera una nueva copia de seguridad completa de la base de datos.
     *
     * @return JsonResponse Respuesta con el estado de la operación y la ruta del archivo generado.
     */
    public function generate(): JsonResponse
    {
        $result = $this->backupService->generate();

        return response()->json($result['data'], $result['status']);
    }

    /**
     * Devuelve el listado de copias de seguridad registradas.
     *
     * @return JsonResponse Respuesta JSON con la colección de respaldos ordenados por fecha.
     */
    public function index(): JsonResponse
    {
        $this->backupService->cleanupLegacyBackupsDirectory();

        $respaldos = RespaldoDato::query()
            ->orderByDesc('fecha_respaldo')
            ->get()
            ->map(fn (RespaldoDato $respaldo) => [
                'id' => $respaldo->getKey(),
                'id_respaldo' => $respaldo->getAttribute('id_respaldo') ?? $respaldo->getKey(),
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
