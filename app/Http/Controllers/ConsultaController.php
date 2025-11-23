<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\HistoriaClinica;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ConsultaController extends Controller
{
    /**
     * Lista las consultas, pudiendo filtrarlas por historia clínica.
     *
     * @param Request $request Solicitud con el parámetro opcional `id_historia` para filtrar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con las consultas formateadas.
     */
    public function index(Request $request)
    {
        $historiaId = $request->query('id_historia');

        $consultas = Consulta::query()
            ->when($historiaId, fn ($query) => $query->where('id_historia', $historiaId))
            ->orderByDesc('fecha_consulta')
            ->orderByDesc('id_consulta')
            ->get()
            ->map(fn (Consulta $consulta) => $this->formatearConsulta($consulta));

        return response()->json(['data' => $consultas]);
    }

    /**
     * Crea una nueva consulta asociada a una historia clínica.
     *
     * @param Request $request Solicitud con fecha, síntomas, diagnóstico, tratamiento y métricas clínicas.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con la consulta guardada y estatus 201.
     */
    public function store(Request $request)
    {
        $validated = $this->validarConsulta($request);

        $historia = HistoriaClinica::findOrFail($validated['id_historia']);

        $consulta = new Consulta();
        $consulta->fill($validated);
        $consulta->fecha_consulta = $this->parsearFecha($validated['fecha_consulta']);
        $consulta->save();

        $historia->touch();

        return response()->json([
            'success' => true,
            'consulta' => $this->formatearConsulta($consulta),
        ], 201);
    }

    /**
     * Devuelve una consulta específica.
     *
     * @param Consulta $consulta Consulta inyectada desde la ruta.
     * @return \Illuminate\Http\JsonResponse Respuesta con la consulta formateada.
     */
    public function show(Consulta $consulta)
    {
        return response()->json([
            'consulta' => $this->formatearConsulta($consulta),
        ]);
    }

    /**
     * Actualiza los datos de una consulta existente.
     *
     * @param Request $request Solicitud con la información validada de la consulta.
     * @param Consulta $consulta Consulta a modificar, inyectada automáticamente.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con la consulta actualizada.
     */
    public function update(Request $request, Consulta $consulta)
    {
        $validated = $this->validarConsulta($request, $consulta);

        if ($consulta->id_historia !== (int) $validated['id_historia']) {
            HistoriaClinica::findOrFail($validated['id_historia']);
            $consulta->id_historia = $validated['id_historia'];
        }

        $consulta->fill($validated);
        $consulta->fecha_consulta = $this->parsearFecha($validated['fecha_consulta']);
        $consulta->save();
        $consulta->loadMissing('historiaClinica');
        $consulta->historiaClinica?->touch();

        return response()->json([
            'success' => true,
            'consulta' => $this->formatearConsulta($consulta),
        ]);
    }

    /**
     * Elimina una consulta y actualiza la fecha de modificación de la historia asociada.
     *
     * @param Consulta $consulta Consulta a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON indicando éxito.
     */
    public function destroy(Consulta $consulta)
    {
        $consulta->loadMissing('historiaClinica');
        $historia = $consulta->historiaClinica;
        $consulta->delete();
        $historia?->touch();

        return response()->json(['success' => true]);
    }

    /**
     * Obtiene todas las consultas pertenecientes a una historia clínica concreta.
     *
     * @param int $historiaId Identificador de la historia clínica.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con las consultas formateadas y ordenadas.
     */
    public function porHistoria($historiaId)
    {
        $historia = HistoriaClinica::findOrFail($historiaId);

        $consultas = $historia->consultas()
            ->orderByDesc('fecha_consulta')
            ->orderByDesc('id_consulta')
            ->get()
            ->map(fn (Consulta $consulta) => $this->formatearConsulta($consulta));

        return response()->json(['data' => $consultas]);
    }

    /**
     * Valida los campos permitidos para registrar o actualizar una consulta.
     *
     * @param Request $request Solicitud con los datos clínicos.
     * @param Consulta|null $consulta Consulta existente cuando se trata de una actualización.
     * @return array Arreglo con los datos validados listos para asignar al modelo.
     */
    private function validarConsulta(Request $request, ?Consulta $consulta = null): array
    {
        return $request->validate([
            'id_historia' => ['required', 'integer', Rule::exists('historia_clinicas', 'id_historia')],
            'fecha_consulta' => ['required', 'date'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric', 'min:0'],
            'temperatura' => ['nullable', 'numeric'],
        ]);
    }

    /**
     * Convierte la fecha recibida en instancia de Carbon o null.
     *
     * @param mixed $fecha Fecha en formato cadena.
     * @return Carbon|null Fecha parseada para almacenar en base de datos.
     */
    private function parsearFecha($fecha): ?Carbon
    {
        return $fecha ? Carbon::parse($fecha) : null;
    }

    /**
     * Estructura la consulta en un arreglo legible para el frontend.
     *
     * @param Consulta $consulta Consulta a transformar.
     * @return array Datos preparados con formato de fechas y campos clínicos.
     */
    private function formatearConsulta(Consulta $consulta): array
    {
        return [
            'id' => $consulta->id_consulta,
            'id_historia' => $consulta->id_historia,
            'fecha' => optional($consulta->fecha_consulta)->toDateString(),
            'fecha_legible' => optional($consulta->fecha_consulta)->format('d/m/Y'),
            'sintomas' => $consulta->sintomas,
            'diagnostico' => $consulta->diagnostico,
            'tratamiento' => $consulta->tratamiento,
            'observaciones' => $consulta->observaciones,
            'peso' => $consulta->peso,
            'temperatura' => $consulta->temperatura,
        ];
    }
}
