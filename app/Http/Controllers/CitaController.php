<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Cita;
use Carbon\Carbon;

/**
 * Controlador REST encargado de gestionar las operaciones CRUD y auxiliares sobre las citas.
 * Todas las respuestas JSON retornan estructuras listas para pintar en componentes dinámicos.
 */
class CitaController extends Controller
{
    /**
     * Catálogo cerrado de estados permitidos para asegurar consistencia en las transiciones.
     */
    private const ESTADOS_PERMITIDOS = ['Pendiente', 'Atendida', 'Cancelada', 'Reprogramada'];

    /**
     * Devuelve el listado completo de citas con filtros opcionales por nombre de mascota o propietario.
     * Se utiliza principalmente para alimentar tablas dinámicas en el frontend.
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        // Se cargan las relaciones necesarias para evitar consultas adicionales en la serialización.
        $citasQuery = Cita::with(['historiaClinica.mascota.propietario']);

        if ($search !== '') {
            // Agrupa la lógica de búsqueda para coincidir por mascota o por propietario completo.
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
     * Obtiene únicamente las citas pendientes dentro de los próximos tres días para recordatorios.
     */
    public function upcoming(Request $request)
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(3);

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
     * Vista tradicional con todas las citas registradas.
     */
    public function index()
    {
        $citas = Cita::all();
        return view('citas.index', compact('citas'));
    }

    /**
     * Muestra el formulario HTML para registrar una nueva cita manualmente.
     */
    public function create()
    {
        return view('citas.create');
    }

    /**
     * Persiste una nueva cita aplicando las validaciones de disponibilidad de datos mínimos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:255'],
            'id_historia' => ['required', 'exists:historia_clinicas,id_historia'],
        ]);

        // Se homogeneiza la hora para cumplir con el formato almacenado en base de datos.
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
     * Renderiza una cita específica en la vista de detalle.
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    /**
     * Carga la cita en un formulario editable.
     */
    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    /**
     * Aplica cambios a una cita existente desde el formulario tradicional.
     */
    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'id_historia' => ['required', 'exists:historia_clinicas,id_historia'],
        ]);

        $cita->update([
            'fecha_cita' => $request->fecha_cita,
            'hora_cita' => $request->hora_cita,
            'id_historia' => $request->id_historia,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    /**
     * Elimina una cita y responde según se trate de una petición web tradicional o AJAX.
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
     * Permite modificar el estado de una cita y manejar reglas de negocio para cada transición.
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

        // Una vez atendida, una cita no puede volver a estados anteriores.
        if ($estadoActual === 'Atendida' && $nuevoEstado !== 'Atendida') {
            throw ValidationException::withMessages([
                'estado' => ['Las citas atendidas no se pueden modificar.'],
            ]);
        }

        // Evita operaciones innecesarias cuando no hay cambios.
        if ($estadoActual === 'Atendida' && $nuevoEstado === 'Atendida') {
            return response()->json([
                'message' => 'La cita ya se encuentra marcada como atendida.',
                'cita' => $this->transformCita($cita->fresh(['historiaClinica.mascota.propietario'])),
            ]);
        }

        if ($nuevoEstado === 'Reprogramada') {
            $nuevaFecha = $validated['fecha_cita'] ?? null;
            $nuevaHora = $validated['hora_cita'] ?? null;

            // Validaciones manuales para asegurar que se seleccione fecha y hora en la reprogramación.
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
     * Formatea una instancia de cita y sus relaciones en un arreglo listo para el frontend.
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
     * Normaliza cadenas de hora HH:MM convirtiéndolas a HH:MM:SS o devolviendo null si no hay dato.
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
