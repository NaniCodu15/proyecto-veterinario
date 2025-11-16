<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VacunaController extends Controller
{
    /**
     * Lista todas las vacunas registradas en el sistema.
     *
     * @return void Se espera renderizar la vista de índice con las vacunas disponibles.
     */
    public function index()
    {
        //
    }

    /**
     * Muestra el formulario para registrar una nueva vacuna.
     *
     * @return void Debe devolver la vista de creación para capturar los datos de la vacuna.
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una vacuna después de validar los datos de entrada.
     *
     * @param Request $request Solicitud con información como mascota, nombre de la vacuna y fechas de aplicación.
     * @return void Previsto para redirigir al listado o responder en JSON según la petición.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra los detalles de una vacuna específica.
     *
     * @param string $id Identificador de la vacuna a consultar.
     * @return void Debería cargar la vista de detalle con la información completa.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Presenta el formulario para editar una vacuna existente.
     *
     * @param string $id Identificador de la vacuna que se desea modificar.
     * @return void Espera entregar la vista de edición con los datos actuales.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza los datos de una vacuna tras validar la solicitud.
     *
     * @param Request $request Solicitud con los nuevos valores de la vacuna.
     * @param string $id Identificador de la vacuna a actualizar.
     * @return void Debe responder con redirección o JSON indicando el resultado.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina la vacuna indicada del registro.
     *
     * @param string $id Identificador de la vacuna a eliminar.
     * @return void Previsto para redirigir al índice o devolver una respuesta JSON.
     */
    public function destroy(string $id)
    {
        //
    }
}
