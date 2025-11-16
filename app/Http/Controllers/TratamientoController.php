<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use App\Models\Consulta;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    /**
     * Muestra el listado de tratamientos con su consulta asociada.
     *
     * @return \Illuminate\View\View Vista `tratamientos.index` con la colección de tratamientos.
     */
    public function index()
    {
        $tratamientos = Tratamiento::with('consulta')->get();
        return view('tratamientos.index', compact('tratamientos'));
    }

    /**
     * Presenta el formulario para registrar un tratamiento nuevo.
     *
     * @return \Illuminate\View\View Vista `tratamientos.create` con el listado de consultas disponibles.
     */
    public function create()
    {
        $consultas = Consulta::all(); // Para seleccionar a cuál consulta pertenece
        return view('tratamientos.create', compact('consultas'));
    }

    /**
     * Guarda un tratamiento validando que la consulta exista y que los campos requeridos estén completos.
     *
     * @param Request $request Solicitud con los datos del tratamiento y su consulta.
     * @return \Illuminate\Http\RedirectResponse Redirección a `tratamientos.index` con mensaje de éxito.
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
     * Muestra el detalle de un tratamiento concreto.
     *
     * @param Tratamiento $tratamiento Tratamiento a visualizar.
     * @return \Illuminate\View\View Vista `tratamientos.show` con el modelo solicitado.
     */
    public function show(Tratamiento $tratamiento)
    {
        return view('tratamientos.show', compact('tratamiento'));
    }

    /**
     * Despliega el formulario de edición de un tratamiento existente.
     *
     * @param Tratamiento $tratamiento Tratamiento que se editará.
     * @return \Illuminate\View\View Vista `tratamientos.edit` con datos actuales y consultas disponibles.
     */
    public function edit(Tratamiento $tratamiento)
    {
        $consultas = Consulta::all();
        return view('tratamientos.edit', compact('tratamiento', 'consultas'));
    }

    /**
     * Actualiza la información del tratamiento validando la relación con la consulta.
     *
     * @param Request $request Solicitud con los nuevos datos del tratamiento.
     * @param Tratamiento $tratamiento Tratamiento a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de actualización exitosa.
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
     * Elimina un tratamiento registrado.
     *
     * @param Tratamiento $tratamiento Tratamiento que se eliminará.
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de confirmación.
     */
    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->delete();
        return redirect()->route('tratamientos.index')
                         ->with('success', 'Tratamiento eliminado correctamente');
    }
}
