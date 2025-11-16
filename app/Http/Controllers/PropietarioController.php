<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropietarioController extends Controller
{
    /**
     * Presenta el listado completo de propietarios registrados.
     *
     * @return void Previsto para devolver una vista con la colección de propietarios.
     */
    public function index()
    {
        //
    }

    /**
     * Despliega el formulario de creación de un nuevo propietario.
     *
     * @return void Debería renderizar la vista de alta de propietario.
     */
    public function create()
    {
        //
    }

    /**
     * Almacena un nuevo propietario tras validar la información enviada.
     *
     * @param Request $request Solicitud con datos como DNI, nombres, contacto y dirección.
     * @return void La respuesta esperada es una redirección al índice con mensaje de éxito.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra la información detallada de un propietario específico.
     *
     * @param string $id Identificador del propietario a consultar.
     * @return void Previsto para cargar la vista de detalle del propietario.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario para editar un propietario existente.
     *
     * @param string $id Identificador del propietario a editar.
     * @return void Debe entregar la vista con los datos precargados.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza la información de un propietario después de validar los cambios.
     *
     * @param Request $request Solicitud con los nuevos datos del propietario.
     * @param string $id Identificador del propietario a actualizar.
     * @return void Espera redirigir al listado con confirmación de éxito.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina un propietario registrado.
     *
     * @param string $id Identificador del propietario a eliminar.
     * @return void Debería responder con redirección o JSON según el contexto.
     */
    public function destroy(string $id)
    {
        //
    }
}
