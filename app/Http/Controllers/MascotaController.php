<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MascotaController extends Controller
{
    /**
     * Muestra el listado de mascotas registradas.
     *
     * @return void Espera renderizar una vista de índice con todas las mascotas.
     */
    public function index()
    {
        //
    }

    /**
     * Retorna el formulario para crear una nueva mascota.
     *
     * @return void Debería entregar la vista de creación con los campos necesarios.
     */
    public function create()
    {
        //
    }

    /**
     * Guarda una nueva mascota luego de validar los datos de entrada.
     *
     * @param Request $request Solicitud con los datos de la mascota (nombre, especie, propietario, etc.).
     * @return void Debe redirigir al índice de mascotas o responder en JSON según el contexto.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra el detalle de una mascota específica.
     *
     * @param string $id Identificador de la mascota a consultar.
     * @return void Pretende cargar la vista de detalle con la información completa de la mascota.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario de edición para la mascota seleccionada.
     *
     * @param string $id Identificador de la mascota que se desea editar.
     * @return void Debe devolver la vista de edición con los datos precargados.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza los datos de una mascota existente tras validar la petición.
     *
     * @param Request $request Solicitud con los datos actualizados de la mascota.
     * @param string $id Identificador de la mascota a actualizar.
     * @return void Espera redirigir al índice o responder con la mascota modificada.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina la mascota indicada del almacenamiento.
     *
     * @param string $id Identificador de la mascota a eliminar.
     * @return void Debería redirigir con un mensaje de confirmación o responder en JSON.
     */
    public function destroy(string $id)
    {
        //
    }
}
