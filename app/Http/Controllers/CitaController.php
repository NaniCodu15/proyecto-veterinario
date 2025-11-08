<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function list(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

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

        $citas = $citasQuery
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita')
            ->get()
            ->map(fn ($cita) => $this->transformCita($cita))
            ->values();

        return response()->json([
            'data' => $citas,
        ]);
    }

    // Mostrar todas las citas
    public function index()
    {
        $citas = Cita::all();
        return view('citas.index', compact('citas'));
    }

    // Formulario para crear nueva cita
    public function create()
    {
        return view('citas.create');
    }

    // Guardar nueva cita
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_cita' => ['required', 'date'],
            'hora_cita' => ['nullable', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:255'],
            'id_historia' => ['required', 'exists:historia_clinicas,id_historia'],
        ]);

        $hora = $validated['hora_cita'] ?? '00:00';
        if (strlen($hora) === 5) {
            $hora .= ':00';
        }

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

    // Mostrar una cita específica
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    // Formulario para editar cita
    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    // Actualizar cita
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

    // Eliminar cita
    public function destroy(Cita $cita)
    {
        $cita->delete();
        return redirect()->route('citas.index')->with('success', 'Cita eliminada correctamente.');
    }

    public function updateEstado(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'estado' => ['required', 'string', 'max:50'],
        ]);

        $cita->estado = $validated['estado'];
        $cita->save();

        return response()->json([
            'message' => 'Estado de la cita actualizado correctamente.',
            'cita' => $this->transformCita($cita->fresh(['historiaClinica.mascota.propietario'])),
        ]);
    }

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
            'hora' => $hora,
            'motivo' => $cita->motivo,
            'estado' => $cita->estado ?? 'Pendiente',
        ];
    }
}
