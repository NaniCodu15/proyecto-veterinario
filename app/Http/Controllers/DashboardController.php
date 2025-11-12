<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mascota;
use App\Models\Propietario;
use App\Models\HistoriaClinica;
use App\Models\Consulta;
use App\Models\Vacuna;
use App\Models\Cita;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalMascotas = Mascota::count();
        $totalPropietarios = Propietario::count();
        $totalHistorias = HistoriaClinica::count();
        $totalConsultas = Consulta::count();
        $totalVacunas = Vacuna::count();

        // Mascotas con sus relaciones para la tabla
        $mascotas = Mascota::with([
            'propietario',
            'historiaClinica.consultas',
            'historiaClinica.vacunas'
        ])->paginate(10); // Paginación de 10 por página

        $upcomingAppointments = Cita::with(['historiaClinica.mascota.propietario'])
            ->whereDate('fecha_cita', '>=', Carbon::today())
            ->orderBy('fecha_cita')
            ->orderBy('hora_cita')
            ->take(4)
            ->get()
            ->map(function ($cita) {
                $historia = $cita->historiaClinica;
                $mascota = $historia?->mascota;
                $propietario = $mascota?->propietario;

                $nombrePropietario = trim(collect([
                    $propietario?->nombres,
                    $propietario?->apellidos,
                ])->filter()->implode(' '));

                $fecha = $cita->fecha_cita ? Carbon::parse($cita->fecha_cita) : null;
                $hora = $cita->hora_cita ? substr($cita->hora_cita, 0, 5) : null;

                return [
                    'id' => $cita->id_cita,
                    'fecha' => $fecha?->format('d/m'),
                    'fecha_detalle' => $fecha?->format('d/m/Y'),
                    'hora' => $hora,
                    'mascota' => $mascota?->nombre ?? 'Sin mascota',
                    'propietario' => $nombrePropietario !== '' ? $nombrePropietario : 'Sin propietario',
                    'estado' => $cita->estado ?? 'Pendiente',
                    'motivo' => $cita->motivo,
                ];
            });

        return view('dashboard', compact(
            'totalMascotas',
            'totalPropietarios',
            'totalHistorias',
            'totalConsultas',
            'totalVacunas',
            'mascotas',
            'upcomingAppointments',
        ));
    }
}
