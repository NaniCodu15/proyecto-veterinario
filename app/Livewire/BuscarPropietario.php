<?php

namespace App\Livewire;

use App\Models\Propietario;
use Livewire\Component;

class BuscarPropietario extends Component
{
    public string $busqueda = '';

    public array $resultados = [];

    public function updatedBusqueda(): void
    {
        $termino = trim($this->busqueda);

        if ($termino === '') {
            $this->limpiarSeleccion();
            return;
        }

        $this->buscar($termino);
    }

    public function seleccionar(int $propietarioId): void
    {
        $propietario = Propietario::select(['id_propietario', 'nombres', 'apellidos', 'dni', 'telefono', 'direccion'])
            ->find($propietarioId);

        if (! $propietario) {
            return;
        }

        $nombreCompleto = trim(collect([$propietario->nombres, $propietario->apellidos])->filter()->implode(' '));

        $payload = [
            'id' => $propietario->id_propietario,
            'nombre_propietario' => $nombreCompleto,
            'dni_propietario' => $propietario->dni ?? '',
            'telefono_propietario' => $propietario->telefono ?? '',
            'direccion_propietario' => $propietario->direccion ?? '',
        ];

        $this->busqueda = $nombreCompleto !== '' ? $nombreCompleto : ($propietario->dni ?? '');
        $this->resultados = [];

        $this->dispatch('propietarioSeleccionado', $payload);
    }

    public function limpiarSeleccion(): void
    {
        $this->reset(['busqueda', 'resultados']);
        $this->dispatch('nuevoPropietario');
    }

    private function buscar(string $termino): void
    {
        if (mb_strlen($termino) < 2) {
            $this->resultados = [];
            return;
        }

        $this->resultados = Propietario::query()
            ->select(['id_propietario', 'nombres', 'apellidos', 'dni', 'telefono', 'direccion'])
            ->where(function ($query) use ($termino) {
                $query
                    ->whereRaw("CONCAT(TRIM(nombres), ' ', TRIM(apellidos)) LIKE ?", ["%{$termino}%"])
                    ->orWhere('dni', 'like', "%{$termino}%");
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->limit(10)
            ->get()
            ->map(function (Propietario $propietario) {
                $nombre = trim(collect([$propietario->nombres, $propietario->apellidos])->filter()->implode(' '));

                return [
                    'id' => $propietario->id_propietario,
                    'nombre_propietario' => $nombre,
                    'dni_propietario' => $propietario->dni ?? '',
                    'telefono_propietario' => $propietario->telefono ?? '',
                    'direccion_propietario' => $propietario->direccion ?? '',
                ];
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.buscar-propietario');
    }
}
