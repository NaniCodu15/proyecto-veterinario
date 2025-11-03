<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HistoriaClinicaController extends Controller
{
    // ✅ Listar historias para AJAX
    public function list()
    {
        $historias = HistoriaClinica::with('mascota')
            ->orderByDesc('fecha_apertura')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (HistoriaClinica $historia) => $this->formatearHistoria($historia))
            ->values();

        return response()->json(['data' => $historias]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_historia' => ['nullable', 'string', 'max:255'],
            'nombreMascota' => ['required', 'string', 'max:100'],
            'especie' => ['required', Rule::in(['perro', 'gato', 'otro'])],
            'especieOtro' => ['required_if:especie,otro', 'nullable', 'string', 'max:100'],
            'raza' => ['nullable', 'string', 'max:100'],
            'sexo' => ['required', Rule::in(['macho', 'hembra'])],
            'edad' => ['nullable', 'integer', 'min:0', 'max:60'],
            'nombrePropietario' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string', 'max:200'],
            'dni' => ['required', 'string', 'max:15'],
            'peso' => ['nullable', 'numeric', 'min:0'],
            'temperatura' => ['nullable', 'numeric'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
        ], [
            'especieOtro.required_if' => 'Debe especificar la especie de la mascota.',
        ]);

        $especieSeleccionada = $validated['especie'] === 'otro'
            ? $validated['especieOtro']
            : $validated['especie'];

        $especieNormalizada = ucfirst(strtolower($especieSeleccionada));
        $sexoNormalizado = $validated['sexo'] === 'macho' ? 'Macho' : 'Hembra';

        [$nombresPropietario, $apellidosPropietario] = $this->separarNombreCompleto($validated['nombrePropietario']);

        $propietario = Propietario::updateOrCreate(
            ['dni' => $validated['dni']],
            [
                'nombres' => $nombresPropietario,
                'apellidos' => $apellidosPropietario,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
            ]
        );

        $mascota = Mascota::firstOrCreate(
            [
                'nombre' => $validated['nombreMascota'],
                'propietario_id' => $propietario->id_propietario,
            ],
            [
                'especie' => $especieNormalizada,
                'raza' => $validated['raza'] ?? null,
                'sexo' => $sexoNormalizado,
                'fecha_nacimiento' => $this->calcularFechaNacimiento($validated['edad'] ?? null),
                'fecha_registro' => Carbon::now(),
            ]
        );

        $mascota->fill([
            'especie' => $especieNormalizada,
            'raza' => $validated['raza'] ?? null,
            'sexo' => $sexoNormalizado,
        ]);

        if (isset($validated['edad'])) {
            $mascota->fecha_nacimiento = $this->calcularFechaNacimiento($validated['edad']);
        }

        $mascota->save();

        $numeroHistoria = $this->generarNumeroHistoria($validated['numero_historia'] ?? null);

        $historia = HistoriaClinica::create([
            'id_mascota' => $mascota->id_mascota,
            'numero_historia' => $numeroHistoria,
            'fecha_apertura' => Carbon::now(),
            'peso' => $validated['peso'] ?? null,
            'temperatura' => $validated['temperatura'] ?? null,
            'sintomas' => $validated['sintomas'] ?? null,
            'diagnostico' => $validated['diagnostico'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $historia->load('mascota');

        return response()->json([
            'success' => true,
            'historia' => $this->formatearHistoria($historia),
        ], 201);
    }

    // ✅ Obtener 1 registro (para editar)
    public function show($id)
    {
        $historia = HistoriaClinica::with('mascota')->findOrFail($id);
        return response()->json($historia);
    }

    // ✅ Actualizar historia clínica (AJAX)
    public function update(Request $request, $id)
    {
        $historia = HistoriaClinica::findOrFail($id);
        $historia->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        HistoriaClinica::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    private function separarNombreCompleto(string $nombreCompleto): array
    {
        $partes = preg_split('/\s+/', trim($nombreCompleto));

        if (!$partes || count($partes) === 0) {
            return ['', ''];
        }

        $nombres = array_shift($partes);
        $apellidos = implode(' ', $partes);

        if (empty($apellidos)) {
            return [$nombreCompleto, ''];
        }

        return [$nombres, $apellidos];
    }

    private function calcularFechaNacimiento($edad): ?string
    {
        if ($edad === null || $edad === '') {
            return null;
        }

        return Carbon::now()->subYears((int) $edad)->toDateString();
    }

    private function generarNumeroHistoria(?string $numeroHistoria): string
    {
        $numero = $numeroHistoria ?: 'HC-' . Carbon::now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        while (HistoriaClinica::where('numero_historia', $numero)->exists()) {
            $numero = 'HC-' . Carbon::now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        }

        return $numero;
    }

    private function formatearHistoria(HistoriaClinica $historia): array
    {
        return [
            'id' => $historia->id_historia,
            'numero_historia' => $historia->numero_historia,
            'mascota' => optional($historia->mascota)->nombre ?? 'Sin nombre',
            'fecha_apertura' => optional($historia->fecha_apertura)->format('d/m/Y'),
        ];
    }
}
