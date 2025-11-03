<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'vacunas' => ['nullable', 'string'],
            'tratamientos' => ['nullable', 'string'],
        ], [
            'especieOtro.required_if' => 'Debe especificar la especie de la mascota.',
        ]);

        $historia = DB::transaction(function () use ($validated) {
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
            } else {
                $mascota->fecha_nacimiento = null;
            }

            $mascota->save();

            $numeroHistoria = $this->generarNumeroHistoria();

            $historia = HistoriaClinica::create([
                'id_mascota' => $mascota->id_mascota,
                'numero_historia' => $numeroHistoria,
                'fecha_apertura' => Carbon::now(),
                'peso' => $validated['peso'] ?? null,
                'temperatura' => $validated['temperatura'] ?? null,
                'sintomas' => $validated['sintomas'] ?? null,
                'diagnostico' => $validated['diagnostico'] ?? null,
                'vacunas' => $validated['vacunas'] ?? null,
                'tratamientos' => $validated['tratamientos'] ?? null,
                'created_by' => Auth::id(),
            ]);

            return $historia->load('mascota');
        });

        return response()->json([
            'success' => true,
            'historia' => $this->formatearHistoria($historia),
        ], 201);
    }

    // ✅ Obtener 1 registro (para editar)
    public function show($id)
    {
        $historia = HistoriaClinica::with(['mascota.propietario'])->findOrFail($id);

        return response()->json([
            'historia' => $this->formatearHistoriaParaFormulario($historia),
        ]);
    }

    // ✅ Actualizar historia clínica (AJAX)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
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
            'vacunas' => ['nullable', 'string'],
            'tratamientos' => ['nullable', 'string'],
        ], [
            'especieOtro.required_if' => 'Debe especificar la especie de la mascota.',
        ]);

        $historia = HistoriaClinica::with(['mascota.propietario'])->findOrFail($id);

        $historia = DB::transaction(function () use ($validated, $historia) {
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

            $mascota = $historia->mascota ?? new Mascota();
            $mascota->nombre = $validated['nombreMascota'];
            $mascota->especie = $especieNormalizada;
            $mascota->raza = $validated['raza'] ?? null;
            $mascota->sexo = $sexoNormalizado;
            $mascota->propietario_id = $propietario->id_propietario;
            $mascota->fecha_registro = $mascota->fecha_registro ?? Carbon::now();

            if (isset($validated['edad'])) {
                $mascota->fecha_nacimiento = $this->calcularFechaNacimiento($validated['edad']);
            } else {
                $mascota->fecha_nacimiento = null;
            }

            $mascota->save();

            $historia->id_mascota = $mascota->id_mascota;
            $historia->peso = $validated['peso'] ?? null;
            $historia->temperatura = $validated['temperatura'] ?? null;
            $historia->sintomas = $validated['sintomas'] ?? null;
            $historia->diagnostico = $validated['diagnostico'] ?? null;
            $historia->vacunas = $validated['vacunas'] ?? null;
            $historia->tratamientos = $validated['tratamientos'] ?? null;

            $historia->save();

            return $historia->load('mascota');
        });

        return response()->json([
            'success' => true,
            'historia' => $this->formatearHistoria($historia),
        ]);
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

    private function generarNumeroHistoria(): string
    {
        $ultimoId = HistoriaClinica::orderByDesc('id_historia')
            ->lockForUpdate()
            ->value('id_historia') ?? 0;

        return sprintf('HC-%05d', $ultimoId + 1);
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

    private function formatearHistoriaParaFormulario(HistoriaClinica $historia): array
    {
        $mascota = $historia->mascota;
        $propietario = $mascota?->propietario;

        $especie = $mascota?->especie ? strtolower($mascota->especie) : null;
        $especieFormulario = $this->normalizarEspecieParaFormulario($especie);

        return [
            'id' => $historia->id_historia,
            'numero_historia' => $historia->numero_historia,
            'nombreMascota' => $mascota?->nombre ?? '',
            'especie' => $especieFormulario['clave'],
            'especieOtro' => $especieFormulario['otro'],
            'raza' => $mascota?->raza ?? '',
            'sexo' => $mascota?->sexo ? strtolower($mascota->sexo) : null,
            'edad' => $this->calcularEdadDesdeFecha($mascota?->fecha_nacimiento),
            'nombrePropietario' => trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? '')),
            'telefono' => $propietario->telefono ?? '',
            'direccion' => $propietario->direccion ?? '',
            'dni' => $propietario->dni ?? '',
            'peso' => $historia->peso,
            'temperatura' => $historia->temperatura,
            'sintomas' => $historia->sintomas,
            'diagnostico' => $historia->diagnostico,
            'vacunas' => $historia->vacunas,
            'tratamientos' => $historia->tratamientos,
        ];
    }

    private function normalizarEspecieParaFormulario(?string $especie): array
    {
        $especie = $especie ? strtolower($especie) : null;

        return match ($especie) {
            'perro' => ['clave' => 'perro', 'otro' => null],
            'gato' => ['clave' => 'gato', 'otro' => null],
            null, '' => ['clave' => null, 'otro' => null],
            default => ['clave' => 'otro', 'otro' => $especie ? ucfirst($especie) : null],
        };
    }

    private function calcularEdadDesdeFecha($fechaNacimiento): ?int
    {
        if (!$fechaNacimiento) {
            return null;
        }

        return Carbon::parse($fechaNacimiento)->diffInYears(Carbon::now());
    }
}
