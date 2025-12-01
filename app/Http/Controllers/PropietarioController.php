<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
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

    /**
     * Busca propietarios por nombre o DNI y devuelve un JSON compatible con Select2.
     */
    public function search(Request $request)
    {
        $termino = $request->string('q')->toString();

        // API: Eloquent Query Builder para alimentar el autocompletado Select2 con propietarios filtrados por nombre o DNI.
        $propietarios = Propietario::query()
            ->when($termino !== '', function ($query) use ($termino) {
                $query->where(function ($subQuery) use ($termino) {
                    $subQuery
                        ->where('nombres', 'like', "%{$termino}%")
                        ->orWhere('apellidos', 'like', "%{$termino}%")
                        ->orWhere('dni', 'like', "%{$termino}%");
                });
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->limit(20)
            ->get();

        $resultados = $propietarios->map(function (Propietario $propietario) {
            $nombreCompleto = trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? ''));
            $dni = $propietario->dni ?? '';
            $texto = $nombreCompleto !== '' ? $nombreCompleto : 'Propietario sin nombre';

            return [
                'id' => $propietario->id_propietario,
                'text' => $dni !== '' ? sprintf('%s · DNI %s', $texto, $dni) : $texto,
                'nombre_completo' => $nombreCompleto,
                'nombres' => $propietario->nombres,
                'apellidos' => $propietario->apellidos,
                'telefono' => $propietario->telefono,
                'direccion' => $propietario->direccion,
                'dni' => $dni,
            ];
        });

        return response()->json([
            'results' => $resultados,
        ]);
    }
}
