<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\Propietario;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HistoriaClinicaController extends Controller
{
    /**
     * Lista historias clínicas con datos de mascota y propietario para consumo vía AJAX.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con las historias formateadas cronológicamente.
     */
    public function list()
    {
        if (!$this->userHasRole([User::ROLE_ADMIN, User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

        $historias = HistoriaClinica::with(['mascota.propietario'])
            ->orderByDesc('fecha_apertura')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (HistoriaClinica $historia) => $this->formatearHistoria($historia))
            ->values();

        return response()->json(['data' => $historias]);
    }

    /**
     * Registra una nueva historia clínica junto con la mascota y propietario asociados.
     *
     * @param Request $request Solicitud con datos de mascota, propietario y parámetros clínicos iniciales.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con la historia creada y código HTTP 201.
     */
    public function store(Request $request)
    {
        if (!$this->userHasRole([User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

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

    /**
     * Devuelve la información de una historia clínica para su edición.
     *
     * @param int|string $id Identificador de la historia clínica.
     * @return \Illuminate\Http\JsonResponse Respuesta con datos de la historia y sus consultas asociadas.
     */
    public function show($id)
    {
        if (!$this->userHasRole([User::ROLE_ADMIN, User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

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

    /**
     * Actualiza los datos de la historia clínica y sus entidades relacionadas.
     *
     * @param Request $request Solicitud con datos validados de mascota y propietario.
     * @param int|string $id Identificador de la historia clínica a modificar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con la historia actualizada.
     */
    public function update(Request $request, $id)
    {
        if (!$this->userHasRole([User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

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

    /**
     * Elimina una historia clínica específica.
     *
     * @param int|string $id Identificador de la historia a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON de éxito.
     */
    public function destroy($id)
    {
        if (!$this->userHasRole([User::ROLE_ADMIN])) {
            return $this->redirectNoPermission();
        }

        HistoriaClinica::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Muestra la vista detallada de una historia clínica.
     *
     * @param int|string $id Identificador de la historia clínica.
     * @return \Illuminate\View\View Vista `layouts.ver` con enlaces para PDF.
     */
    public function ver($id)
    {
        if (!$this->userHasRole([User::ROLE_ADMIN, User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

        $historia = $this->obtenerHistoriaCompleta($id);

        $codigo = $historia->numero_historia ?: sprintf('HC-%05d', $historia->id_historia);

        return view('layouts.ver', [
            'historia' => $historia,
            'codigo' => $codigo,
            'pdfUrl' => route('historia_clinicas.pdf', ['historia' => $historia->id_historia]),
            'downloadUrl' => route('historia_clinicas.pdf', ['historia' => $historia->id_historia, 'download' => 1]),
        ]);
    }

    /**
     * Genera o muestra el PDF de la historia clínica solicitada.
     *
     * @param Request $request Solicitud que puede incluir `download` para forzar descarga.
     * @param int|string $id Identificador de la historia clínica.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response Stream o descarga del PDF generado.
     */
    public function pdf(Request $request, $id)
    {
        if (!$this->userHasRole([User::ROLE_ADMIN, User::ROLE_ASISTENTE])) {
            return $this->redirectNoPermission();
        }

        $historia = $this->obtenerHistoriaCompleta($id);
        $codigo = $historia->numero_historia ?: sprintf('HC-%05d', $historia->id_historia);

        $datosPdf = $this->prepararDatosPdf($historia, $codigo);

        $pdf = Pdf::loadView('layouts.pdf', $datosPdf)->setPaper('a4');

        $nombreArchivo = 'historia_clinica_' . Str::of($codigo)->replace([' ', '/'], '_') . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($nombreArchivo);
        }

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Divide un nombre completo en nombres y apellidos utilizando espacios como separador.
     *
     * @param string $nombreCompleto Cadena ingresada por el usuario.
     * @return array Arreglo con dos posiciones: [nombres, apellidos].
     */
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

    /**
     * Calcula una fecha de nacimiento aproximada restando años a la fecha actual.
     *
     * @param int|string|null $edad Edad en años proporcionada desde el formulario.
     * @return string|null Fecha estimada de nacimiento o null si no se proporcionó edad.
     */
    private function calcularFechaNacimiento($edad): ?string
    {
        if ($edad === null || $edad === '') {
            return null;
        }

        return Carbon::now()->subYears((int) $edad)->toDateString();
    }

    /**
     * Genera un número de historia incremental con el formato HC-00001.
     *
     * @return string Código único para identificar la historia clínica.
     */
    private function generarNumeroHistoria(): string
    {
        $ultimoId = HistoriaClinica::orderByDesc('id_historia')
            ->lockForUpdate()
            ->value('id_historia') ?? 0;

        return sprintf('HC-%05d', $ultimoId + 1);
    }

    /**
     * Recupera la historia clínica con todas sus relaciones necesarias para vistas y PDFs.
     *
     * @param int|string $id Identificador de la historia clínica.
     * @return HistoriaClinica Modelo cargado con mascota, propietario y consultas.
     */
    private function obtenerHistoriaCompleta($id): HistoriaClinica
    {
        return HistoriaClinica::with([
            'mascota.propietario',
            'consultas' => fn ($query) => $query
                ->orderBy('fecha_consulta')
                ->orderBy('id_consulta'),
        ])->findOrFail($id);
    }

    /**
     * Prepara los datos estructurados que alimentan la plantilla del PDF.
     *
     * @param HistoriaClinica $historia Historia clínica completa.
     * @param string $codigo Código asignado a la historia clínica.
     * @return array Datos listos para ser consumidos por la vista PDF.
     */
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
                'tratamientos_detallados' => collect(),
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

    /**
     * Formatea la historia clínica para mostrarla en listados rápidos.
     *
     * @param HistoriaClinica $historia Instancia a formatear.
     * @return array Datos resumidos con mascota, propietario y fechas.
     */
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

    /**
     * Estructura la historia clínica para completar formularios de edición.
     *
     * @param HistoriaClinica $historia Historia a transformar.
     * @return array Datos con normalización de especie, sexo y edad.
     */
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

    /**
     * Formatea una consulta vinculada a la historia para uso en formularios y listados.
     *
     * @param mixed $consulta Consulta asociada a la historia clínica.
     * @return array Datos clínicos y fechas en formatos legibles.
     */
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

    /**
     * Normaliza la especie almacenada para mostrar correctamente los campos de formulario.
     *
     * @param string|null $especie Valor de especie en la base de datos.
     * @return array Arreglo con la clave seleccionada y el valor personalizado si aplica.
     */
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

    /**
     * Calcula la edad en años a partir de la fecha de nacimiento.
     *
     * @param string|null $fechaNacimiento Fecha en formato fecha o null.
     * @return int|null Edad en años o null cuando no hay fecha.
     */
    private function calcularEdadDesdeFecha($fechaNacimiento): ?int
    {
        if (!$fechaNacimiento) {
            return null;
        }

        return Carbon::parse($fechaNacimiento)->diffInYears(Carbon::now());
    }
}
