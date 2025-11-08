<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\HistoriaClinica;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ConsultaController extends Controller
{
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

    public function show(Consulta $consulta)
    {
        return response()->json([
            'consulta' => $this->formatearConsulta($consulta),
        ]);
    }

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

    public function destroy(Consulta $consulta)
    {
        $consulta->loadMissing('historiaClinica');
        $historia = $consulta->historiaClinica;
        $consulta->delete();
        $historia?->touch();

        return response()->json(['success' => true]);
    }

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

    private function parsearFecha($fecha): ?Carbon
    {
        return $fecha ? Carbon::parse($fecha) : null;
    }

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
