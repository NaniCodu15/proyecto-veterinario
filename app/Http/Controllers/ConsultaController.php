<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\HistoriaClinica;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Controlador API para administrar las consultas médicas asociadas a historias clínicas.
 * Expone operaciones CRUD completas y utilidades de filtrado para alimentar componentes JS.
 */
class ConsultaController extends Controller
{
    /**
     * Lista las consultas con opción de filtrar por historia clínica.
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
     * Registra una nueva consulta médica en la historia indicada.
     */
    public function store(Request $request)
    {
        $validated = $this->validarConsulta($request);

        // Validación adicional para asegurar que la historia exista realmente.
        $historia = HistoriaClinica::findOrFail($validated['id_historia']);

        $consulta = new Consulta();
        $consulta->fill($validated);
        $consulta->fecha_consulta = $this->parsearFecha($validated['fecha_consulta']);
        $consulta->save();

        // Actualiza el timestamp de la historia para reflejar actividad reciente.
        $historia->touch();

        return response()->json([
            'success' => true,
            'consulta' => $this->formatearConsulta($consulta),
        ], 201);
    }

    /**
     * Devuelve los detalles de una consulta específica.
     */
    public function show(Consulta $consulta)
    {
        return response()->json([
            'consulta' => $this->formatearConsulta($consulta),
        ]);
    }

    /**
     * Actualiza una consulta existente manteniendo la integridad de la historia clínica asociada.
     */
    public function update(Request $request, Consulta $consulta)
    {
        $validated = $this->validarConsulta($request, $consulta);

        if ($consulta->id_historia !== (int) $validated['id_historia']) {
            // Si se reasigna la consulta a otra historia se valida y actualiza el vínculo.
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
     * Elimina una consulta y marca la historia como actualizada.
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
     * Acceso rápido a todas las consultas pertenecientes a una historia en particular.
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
     * Centraliza la validación de payloads de consulta, incluyendo medidas y observaciones.
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
     * Convierte cadenas de fecha a instancias Carbon o devuelve null si el campo viene vacío.
     */
    private function parsearFecha($fecha): ?Carbon
    {
        return $fecha ? Carbon::parse($fecha) : null;
    }

    /**
     * Serializa una consulta a un arreglo con atributos legibles y formatos amigables.
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
