<?php

namespace App\Http\Controllers;

use App\Models\RespaldoDato;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de utilidades encargado de orquestar la generación y consulta de respaldos.
 * Se apoya en {@see BackupService} para ejecutar las operaciones pesadas y responde siempre
 * con JSON para facilitar su consumo desde interfaces administrativas.
 */
class BackupController extends Controller
{
    /**
     * Inyección del servicio especializado que ejecuta los comandos de respaldo.
     * Utiliza promoted properties para exponer la dependencia sin crear atributos extra.
     */
    public function __construct(private readonly BackupService $backupService)
    {
    }

    /**
     * Genera una nueva copia de seguridad completa de la base de datos.
     */
    public function generate(): JsonResponse
    {
        // Delegamos en el servicio para mantener la lógica compleja fuera del controlador.
        $result = $this->backupService->generate();

        return response()->json($result['data'], $result['status']);
    }

    /**
     * Devuelve el listado de copias de seguridad registradas.
     */
    public function index(): JsonResponse
    {
        // Antes de listar, se limpian respaldos obsoletos del disco para mantener consistencia.
        $this->backupService->cleanupLegacyBackupsDirectory();

        $respaldos = RespaldoDato::query()
            ->orderByDesc('fecha_respaldo')
            ->get()
            // Se transforma cada modelo en un arreglo simple apto para consumo en frontend.
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
