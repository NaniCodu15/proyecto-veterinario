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
        $resultado = $this->backupService->handleBackup();
        $respaldo = $resultado['respaldo'];

        return response()->json([
            'message' => $resultado['mensaje'],
            'respaldo' => [
                'id' => $respaldo->id,
                'id_respaldo' => $respaldo->id,
                'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
                'nombre_archivo' => $respaldo->nombre_archivo,
                'ruta_archivo' => $respaldo->ruta_archivo,
                'estado' => $respaldo->estado,
            ],
        ], $resultado['code']);
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
                'id_respaldo' => $respaldo->id,
                'fecha_respaldo' => optional($respaldo->fecha_respaldo)->toIso8601String(),
                'nombre_archivo' => $respaldo->nombre_archivo,
                'ruta_archivo' => $respaldo->ruta_archivo,
                'estado' => $respaldo->estado,
            ]);

        return response()->json([
            'data' => $respaldos,
        ]);
    }

    public function download(RespaldoDato $respaldo)
    {
        return $this->backupService->downloadBackup($respaldo);
    }
}
