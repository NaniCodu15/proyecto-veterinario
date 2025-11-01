<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Consulta;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    // Mostrar todos los tratamientos
    public function index()
    {
        $tratamientos = Tratamiento::with('consulta')->get();
        return view('tratamientos.index', compact('tratamientos'));
    }

    // Formulario para crear un nuevo tratamiento
    public function create()
    {
        $consultas = Consulta::all(); // Para seleccionar a cuál consulta pertenece
        return view('tratamientos.create', compact('consultas'));
    }

    // Guardar tratamiento nuevo
    public function store(Request $request)
    {
        $request->validate([
            'id_consulta' => 'required|exists:consultas,id_consulta',
            'medicamento' => 'required|string|max:150',
            'dosis' => 'nullable|string|max:100',
            'duracion' => 'nullable|string|max:100',
            'indicaciones' => 'nullable|string',
        ]);

        Tratamiento::create($request->all());

        return redirect()->route('tratamientos.index')
                         ->with('success', 'Tratamiento creado correctamente');
    }

    // Mostrar un tratamiento específico
    public function show(Tratamiento $tratamiento)
    {
        return view('tratamientos.show', compact('tratamiento'));
    }

    // Formulario para editar
    public function edit(Tratamiento $tratamiento)
    {
        $consultas = Consulta::all();
        return view('tratamientos.edit', compact('tratamiento', 'consultas'));
    }

    // Actualizar tratamiento
    public function update(Request $request, Tratamiento $tratamiento)
    {
        $request->validate([
            'id_consulta' => 'required|exists:consultas,id_consulta',
            'medicamento' => 'required|string|max:150',
            'dosis' => 'nullable|string|max:100',
            'duracion' => 'nullable|string|max:100',
            'indicaciones' => 'nullable|string',
        ]);

        $tratamiento->update($request->all());

        return redirect()->route('tratamientos.index')
                         ->with('success', 'Tratamiento actualizado correctamente');
    }

    // Eliminar tratamiento
    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->delete();
        return redirect()->route('tratamientos.index')
                         ->with('success', 'Tratamiento eliminado correctamente');
    }
}
