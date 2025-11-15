<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\Propietario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HistoriaClinicaController extends Controller
{
    // ✅ Listar historias para AJAX
    public function list()
    {
        $historias = HistoriaClinica::with(['mascota.propietario'])
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
            'raza' => ['required', 'string', 'max:100'],
            'sexo' => ['required', Rule::in(['macho', 'hembra'])],
            'edad' => ['nullable', 'integer', 'min:0', 'max:60'],
            'peso' => ['required', 'numeric', 'min:0'],
            'nombrePropietario' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string', 'max:200'],
            'dni' => ['required', 'string', 'max:15'],
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
                    'telefono' => $validated['telefono'],
                    'direccion' => $validated['direccion'],
                ]
            );

            $mascota = Mascota::firstOrCreate(
                [
                    'nombre' => $validated['nombreMascota'],
                    'propietario_id' => $propietario->id_propietario,
                ],
                [
                    'especie' => $especieNormalizada,
                    'raza' => $validated['raza'],
                    'sexo' => $sexoNormalizado,
                    'fecha_nacimiento' => $this->calcularFechaNacimiento($validated['edad'] ?? null),
                    'fecha_registro' => Carbon::now(),
                ]
            );

            $mascota->fill([
                'especie' => $especieNormalizada,
                'raza' => $validated['raza'],
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
                'created_by' => Auth::id(),
            ]);

            return $historia->load(['mascota.propietario']);
        });

        return response()->json([
            'success' => true,
            'historia' => $this->formatearHistoria($historia),
        ], 201);
    }

    // ✅ Obtener 1 registro (para editar)
    public function show($id)
    {
        $historia = HistoriaClinica::with([
            'mascota.propietario',
            'consultas' => fn ($query) => $query
                ->orderByDesc('fecha_consulta')
                ->orderByDesc('id_consulta'),
        ])->findOrFail($id);

        return response()->json([
            'historia' => $this->formatearHistoriaParaFormulario($historia),
            'consultas' => $historia->consultas
                ->map(fn ($consulta) => $this->formatearConsulta($consulta))
                ->values(),
        ]);
    }

    // ✅ Actualizar historia clínica (AJAX)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombreMascota' => ['required', 'string', 'max:100'],
            'especie' => ['required', Rule::in(['perro', 'gato', 'otro'])],
            'especieOtro' => ['required_if:especie,otro', 'nullable', 'string', 'max:100'],
            'raza' => ['required', 'string', 'max:100'],
            'sexo' => ['required', Rule::in(['macho', 'hembra'])],
            'edad' => ['nullable', 'integer', 'min:0', 'max:60'],
            'peso' => ['required', 'numeric', 'min:0'],
            'nombrePropietario' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string', 'max:200'],
            'dni' => ['required', 'string', 'max:15'],
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
                    'telefono' => $validated['telefono'],
                    'direccion' => $validated['direccion'],
                ]
            );

            $mascota = $historia->mascota ?? new Mascota();
            $mascota->nombre = $validated['nombreMascota'];
            $mascota->especie = $especieNormalizada;
            $mascota->raza = $validated['raza'];
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

            $historia->save();

            return $historia->load(['mascota.propietario']);
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

    public function ver($id)
    {
        $historia = $this->obtenerHistoriaCompleta($id);

        $codigo = $historia->numero_historia ?: sprintf('HC-%05d', $historia->id_historia);

        return view('historia_clinicas.ver', [
            'historia' => $historia,
            'codigo' => $codigo,
            'pdfUrl' => route('historia_clinicas.pdf', ['historia' => $historia->id_historia]),
            'downloadUrl' => route('historia_clinicas.pdf', ['historia' => $historia->id_historia, 'download' => 1]),
        ]);
    }

    public function pdf(Request $request, $id)
    {
        $historia = $this->obtenerHistoriaCompleta($id);
        $codigo = $historia->numero_historia ?: sprintf('HC-%05d', $historia->id_historia);

        $datosPdf = $this->prepararDatosPdf($historia, $codigo);

        $pdf = Pdf::loadView('historia_clinicas.pdf', $datosPdf)->setPaper('a4');

        $nombreArchivo = 'historia_clinica_' . Str::of($codigo)->replace([' ', '/'], '_') . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($nombreArchivo);
        }

        return $pdf->stream($nombreArchivo);
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

    private function obtenerHistoriaCompleta($id): HistoriaClinica
    {
        return HistoriaClinica::with([
            'mascota.propietario',
            'consultas' => fn ($query) => $query
                ->orderBy('fecha_consulta')
                ->orderBy('id_consulta'),
            'consultas.tratamientos' => fn ($query) => $query->orderBy('id_tratamiento'),
        ])->findOrFail($id);
    }

    private function prepararDatosPdf(HistoriaClinica $historia, string $codigo): array
    {
        $mascota = $historia->mascota;
        $propietario = $mascota?->propietario;

        $consultas = $historia->consultas->map(function ($consulta) {
            return [
                'fecha' => optional($consulta->fecha_consulta)->format('d/m/Y'),
                'hora' => optional($consulta->fecha_consulta)->format('H:i'),
                'sintomas' => $consulta->sintomas,
                'diagnostico' => $consulta->diagnostico,
                'tratamiento' => $consulta->tratamiento,
                'observaciones' => $consulta->observaciones,
                'peso' => $consulta->peso,
                'temperatura' => $consulta->temperatura,
                'tratamientos_detallados' => $consulta->tratamientos->map(fn ($tratamiento) => [
                    'medicamento' => $tratamiento->medicamento,
                    'dosis' => $tratamiento->dosis,
                    'duracion' => $tratamiento->duracion,
                    'indicaciones' => $tratamiento->indicaciones,
                ])->filter(function ($datos) {
                    return collect($datos)->filter()->isNotEmpty();
                })->values(),
            ];
        })->values();

        $edad = $this->calcularEdadDesdeFecha($mascota?->fecha_nacimiento);

        return [
            'codigo' => $codigo,
            'historia' => $historia,
            'propietario' => [
                'nombre' => trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? '')) ?: '—',
                'dni' => $propietario->dni ?? '—',
                'telefono' => $propietario->telefono ?? '—',
                'direccion' => $propietario->direccion ?? '—',
            ],
            'mascota' => [
                'nombre' => $mascota->nombre ?? '—',
                'especie' => $mascota->especie ?? '—',
                'raza' => $mascota->raza ?? '—',
                'sexo' => $mascota->sexo ?? '—',
                'edad' => $edad !== null ? $edad . ' año' . ($edad === 1 ? '' : 's') : '—',
                'peso' => $historia->peso ?? '—',
            ],
            'fecha_apertura' => optional($historia->fecha_apertura)->format('d/m/Y') ?? '—',
            'consultas' => $consultas,
            'logoPath' => public_path('images/logo.png'),
            'fecha_emision' => Carbon::now()->format('d/m/Y H:i'),
        ];
    }

    private function formatearHistoria(HistoriaClinica $historia): array
    {
        $mascota = $historia->mascota;
        $propietario = $mascota?->propietario;
        $nombrePropietario = trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? ''));

        return [
            'id' => $historia->id_historia,
            'numero_historia' => $historia->numero_historia,
            'mascota' => $mascota?->nombre ?? 'Sin nombre',
            'propietario' => $nombrePropietario !== '' ? $nombrePropietario : 'Sin propietario',
            'propietario_dni' => $propietario->dni ?? null,
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
            'id_mascota' => $mascota?->id_mascota,
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
            'fecha_apertura' => optional($historia->fecha_apertura)->format('d/m/Y'),
        ];
    }

    private function formatearConsulta($consulta): array
    {
        return [
            'id' => $consulta->id_consulta,
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
