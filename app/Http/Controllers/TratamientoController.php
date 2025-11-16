<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Consulta;
use Illuminate\Http\Request;

/**
 * Controlador CRUD clásico para los tratamientos prescritos en cada consulta.
 * Expone vistas blade tradicionales y aplica validaciones básicas.
 */
class TratamientoController extends Controller
{
    /**
     * Lista todos los tratamientos cargando la consulta relacionada para mostrar contexto clínico.
     */
    public function index()
    {
        $tratamientos = Tratamiento::with('consulta')->get();
        return view('tratamientos.index', compact('tratamientos'));
    }

    /**
     * Muestra el formulario para crear un tratamiento, entregando las consultas disponibles.
     */
    public function create()
    {
        $consultas = Consulta::all(); // Para seleccionar a cuál consulta pertenece
        return view('tratamientos.create', compact('consultas'));
    }

    /**
     * Valida y almacena un nuevo tratamiento asociado a una consulta.
     */
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

    /**
     * Visualiza los datos completos de un tratamiento específico.
     */
    public function show(Tratamiento $tratamiento)
    {
        return view('tratamientos.show', compact('tratamiento'));
    }

    /**
     * Renderiza el formulario de edición con la lista de consultas.
     */
    public function edit(Tratamiento $tratamiento)
    {
        $consultas = Consulta::all();
        return view('tratamientos.edit', compact('tratamiento', 'consultas'));
    }

    /**
     * Actualiza los datos del tratamiento validando nuevamente las reglas de negocio básicas.
     */
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

    /**
     * Elimina un tratamiento determinado.
     */
    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->delete();
        return redirect()->route('tratamientos.index')
                         ->with('success', 'Tratamiento eliminado correctamente');
    }
}
