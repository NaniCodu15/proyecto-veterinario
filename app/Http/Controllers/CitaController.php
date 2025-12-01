<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Cita;
use Carbon\Carbon;

class CitaController extends Controller
{
    private const ESTADOS_PERMITIDOS = ['Pendiente', 'Atendida', 'Cancelada', 'Reprogramada'];

    /**
     * Lista todas las citas filtrando opcionalmente por el parámetro de búsqueda.
     *
     * @param Request $request Solicitud HTTP que puede incluir el parámetro `q` para buscar por nombre de mascota o propietario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el arreglo de citas formateadas para la tabla administrativa.
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        // API: Eloquent ORM con carga ansiosa para exponer citas y relaciones a la tabla del panel administrativo.
        $citasQuery = Cita::with(['historiaClinica.mascota.propietario']);

        if ($search !== '') {
            $citasQuery->where(function ($query) use ($search) {
                $query
                    ->whereHas('historiaClinica.mascota', function ($mascotaQuery) use ($search) {
                        $mascotaQuery->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('historiaClinica.mascota.propietario', function ($propietarioQuery) use ($search) {
                        $propietarioQuery->where(function ($propietarioSubQuery) use ($search) {
                            $propietarioSubQuery
                                ->where('nombres', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhereRaw("CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, '')) LIKE ?", ["%{$search}%"]);
                        });
                    });
            });
        }

        $ordenEstado = "CASE\n                WHEN estado = 'Pendiente' THEN 0\n                WHEN estado = 'Reprogramada' THEN 1\n                WHEN estado = 'Atendida' THEN 2\n                WHEN estado = 'Cancelada' THEN 3\n                ELSE 4\n            END";

        $citas = $citasQuery
            ->orderByRaw($ordenEstado)
            ->orderBy('fecha_cita')
            ->orderBy('hora_cita')
            ->get()
            ->map(fn ($cita) => $this->transformCita($cita))
            ->values();

        return response()->json([
            'data' => $citas,
        ]);
    }

    /**
     * Obtiene las próximas citas pendientes dentro de los tres días siguientes.
     *
     * @param Request $request Solicitud HTTP sin parámetros adicionales.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con las citas ordenadas por fecha y hora.
     */
    public function upcoming(Request $request)
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(3);

        // API: Eloquent ORM para listar próximas citas pendientes usadas por el widget de recordatorios.
        $citas = Cita::with(['historiaClinica.mascota.propietario'])
            ->where('estado', 'Pendiente')
            ->whereBetween('fecha_cita', [$today, $limitDate])
            ->orderBy('fecha_cita')
            ->orderBy('hora_cita')
            ->get()
            ->map(fn ($cita) => $this->transformCita($cita))
            ->values();

        return response()->json([
            'data' => $citas,
        ]);
    }

    /**
     * Muestra la vista con el listado completo de citas.
     *
     * @return \Illuminate\View\View Vista `citas.index` con todas las citas cargadas.
     */
    public function index()
    {
        $citas = Cita::all();
        return view('citas.index', compact('citas'));
    }

    /**
     * Retorna el formulario para registrar una nueva cita.
     *
     * @return \Illuminate\View\View Vista `citas.create` sin datos adicionales.
     */
    public function create()
    {
        return view('citas.create');
    }

    /**
     * Almacena una nueva cita después de validar los datos requeridos.
     *
     * @param Request $request Solicitud con fecha, hora, motivo e identificador de historia clínica.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse Respuesta JSON cuando es una petición AJAX o redirección a `citas.index`.
     */
    public function store(Request $request)
    {
        // API: Validador de Laravel para asegurar integridad de fecha, hora y referencia clínica al crear la cita.
        $validated = $request->validate([
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:255'],
            'id_historia' => ['required', 'exists:historia_clinicas,id_historia'],
        ]);

        $hora = $this->normalizarHora($validated['hora_cita'] ?? null) ?? '00:00:00';

        $cita = Cita::create([
            'fecha_cita' => $validated['fecha_cita'],
            'hora_cita' => $hora,
            'motivo' => $validated['motivo'],
            'id_historia' => $validated['id_historia'],
            'estado' => 'Pendiente',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cita creada correctamente.',
                'cita' => $cita,
            ], 201);
        }

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    /**
     * Muestra la vista con el detalle de una cita concreta.
     *
     * @param Cita $cita Modelo inyectado con la cita solicitada.
     * @return \Illuminate\View\View Vista `citas.show` con la cita a visualizar.
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    /**
     * Presenta el formulario de edición para una cita existente.
     *
     * @param Cita $cita Modelo de la cita a modificar.
     * @return \Illuminate\View\View Vista `citas.edit` con los datos precargados.
     */
    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    /**
     * Actualiza los datos básicos de la cita luego de validar la información.
     *
     * @param Request $request Solicitud con fecha, hora y motivo de la cita.
     * @param Cita $cita Instancia de la cita a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirección a `citas.index` con mensaje de éxito.
     */
    public function update(Request $request, Cita $cita)
    {
        // API: Validador de Laravel para controlar los campos editables de la cita desde el panel administrativo.
        $validated = $request->validate([
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|date_format:H:i',
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $hora = $this->normalizarHora($validated['hora_cita'] ?? null) ?? $cita->hora_cita;

        $cita->update([
            'fecha_cita' => $validated['fecha_cita'],
            'hora_cita' => $hora,
            'motivo' => $validated['motivo'] ?? $cita->motivo,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada correctamente.',
                'cita' => $this->transformCita($cita->fresh(['historiaClinica.mascota.propietario'])),
            ]);
        }

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    /**
     * Elimina la cita indicada y retorna la respuesta adecuada al contexto.
     *
     * @param Request $request Solicitud HTTP que determina si la respuesta será JSON.
     * @param Cita $cita Cita que se desea eliminar.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse Respuesta en JSON o redirección con mensaje de éxito.
     */
    public function destroy(Request $request, Cita $cita)
    {
        $cita->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cita anulada correctamente.',
            ]);
        }

        return redirect()->route('citas.index')->with('success', 'Cita eliminada correctamente.');
    }

    /**
     * Actualiza únicamente el estado de la cita y permite reprogramar cuando corresponde.
     *
     * @param Request $request Solicitud con el nuevo estado y opcionalmente nueva fecha y hora.
     * @param Cita $cita Cita cuyo estado se modificará.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el mensaje y la cita formateada.
     * @throws ValidationException Cuando se intenta modificar una cita atendida o reprogramar sin fecha/hora.
     */
    public function updateEstado(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'estado' => ['required', 'string', Rule::in(self::ESTADOS_PERMITIDOS)],
            'fecha_cita' => ['nullable', 'date'],
            'hora_cita' => ['nullable', 'date_format:H:i'],
        ]);

        $estadoActual = $cita->estado ?? 'Pendiente';
        $nuevoEstado = $validated['estado'];

        if ($estadoActual === 'Atendida' && $nuevoEstado !== 'Atendida') {
            throw ValidationException::withMessages([
                'estado' => ['Las citas atendidas no se pueden modificar.'],
            ]);
        }

        if ($estadoActual === 'Atendida' && $nuevoEstado === 'Atendida') {
            return response()->json([
                'message' => 'La cita ya se encuentra marcada como atendida.',
                'cita' => $this->transformCita($cita->fresh(['historiaClinica.mascota.propietario'])),
            ]);
        }

        if ($nuevoEstado === 'Cancelada') {
            $cita->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cita cancelada y eliminada correctamente.',
                    'deleted' => true,
                    'redirect' => route('citas.index'),
                ]);
            }

            return redirect()
                ->route('citas.index')
                ->with('success', 'Cita cancelada y eliminada correctamente.');
        }

        if ($nuevoEstado === 'Reprogramada') {
            $nuevaFecha = $validated['fecha_cita'] ?? null;
            $nuevaHora = $validated['hora_cita'] ?? null;

            $errores = [];
            if (!$nuevaFecha) {
                $errores['fecha_cita'] = ['Selecciona la nueva fecha de la cita.'];
            }

            if (!$nuevaHora) {
                $errores['hora_cita'] = ['Selecciona la nueva hora de la cita.'];
            }

            if (!empty($errores)) {
                throw ValidationException::withMessages($errores);
            }

            $cita->fecha_cita = $nuevaFecha;
            $cita->hora_cita = $this->normalizarHora($nuevaHora) ?? $cita->hora_cita;
        }

        $cita->estado = $nuevoEstado;
        $cita->save();

        return response()->json([
            'message' => 'Estado de la cita actualizado correctamente.',
            'cita' => $this->transformCita($cita->fresh(['historiaClinica.mascota.propietario'])),
        ]);
    }

    /**
     * Formatea la información de la cita para consumo por el frontend.
     *
     * @param Cita $cita Cita con sus relaciones cargadas.
     * @return array Arreglo con campos de identificación, datos de mascota, propietario y programación.
     */
    private function transformCita(Cita $cita): array
    {
        $cita->loadMissing(['historiaClinica.mascota.propietario']);

        $historia = $cita->historiaClinica;
        $mascota = $historia?->mascota;
        $propietario = $mascota?->propietario;

        $nombrePropietario = trim(collect([$propietario?->nombres, $propietario?->apellidos])->filter()->implode(' '));
        $telefono = (string) ($propietario->telefono ?? '');
        $telefonoWhatsapp = preg_replace('/\D+/', '', $telefono) ?? '';

        $fecha = $cita->fecha_cita ? Carbon::parse($cita->fecha_cita) : null;
        $hora = $cita->hora_cita ? substr($cita->hora_cita, 0, 5) : null;

        return [
            'id' => $cita->id_cita,
            'historia_id' => $historia->id_historia ?? null,
            'numero_historia' => $historia->numero_historia ?? null,
            'mascota' => $mascota->nombre ?? null,
            'propietario' => $nombrePropietario !== '' ? $nombrePropietario : null,
            'propietario_dni' => $propietario->dni ?? null,
            'propietario_telefono' => $telefono !== '' ? $telefono : null,
            'propietario_whatsapp' => $telefonoWhatsapp !== '' ? $telefonoWhatsapp : null,
            'fecha' => $fecha?->toDateString(),
            'fecha_legible' => $fecha?->format('d/m/Y'),
            'fecha_corta' => $fecha?->format('d/m'),
            'hora' => $hora,
            'motivo' => $cita->motivo,
            'estado' => $cita->estado ?? 'Pendiente',
        ];
    }

    /**
     * Normaliza una hora asegurando el formato HH:MM:SS.
     *
     * @param string|null $hora Hora recibida desde el formulario.
     * @return string|null Cadena normalizada o null si no se proporcionó hora.
     */
    private function normalizarHora(?string $hora): ?string
    {
        if ($hora === null || $hora === '') {
            return null;
        }

        $hora = trim($hora);

        if (strlen($hora) === 5) {
            return $hora . ':00';
        }

        return $hora;
    }
}
