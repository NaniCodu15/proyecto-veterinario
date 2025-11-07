<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Cita;

class CitaController extends Controller
{
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
}
