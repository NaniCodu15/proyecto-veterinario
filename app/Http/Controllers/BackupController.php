<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backupService)
    {
    }

    /**
     * Genera una nueva copia de seguridad completa de la base de datos.
     */
    public function generate(): JsonResponse
    {
        $result = $this->backupService->generate();

        return response()->json($result['data'], $result['status']);
    }

    /**
     * Devuelve el listado de copias de seguridad registradas.
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
