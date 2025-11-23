<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mascota;
use App\Models\Propietario;
use App\Models\HistoriaClinica;
use App\Models\Consulta;
use App\Models\Cita;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Compila las estadísticas generales y listados recientes para el panel principal.
     *
     * @return \Illuminate\View\View Vista `dashboard` con métricas, paginación de mascotas y citas próximas.
     */
    public function index()
    {
        // Estadísticas generales
        $totalMascotas = Mascota::count();
        $totalPropietarios = Propietario::count();
        $totalHistorias = HistoriaClinica::count();
        $totalConsultas = Consulta::count();

        // Historias clínicas con mascota y propietario para la sección de historias registradas
        $historias = HistoriaClinica::with(['mascota.propietario'])
            ->orderByDesc('fecha_apertura')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (HistoriaClinica $historia) {
                $mascota = $historia->mascota;
                $propietario = $mascota?->propietario;
                $nombrePropietario = trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? ''));

                return [
                    'id' => $historia->id_historia,
                    'numero_historia' => $historia->numero_historia,
                    'mascota' => $mascota?->nombre ?? 'Sin nombre',
                    'propietario' => $nombrePropietario !== '' ? $nombrePropietario : 'Sin propietario',
                    'propietario_dni' => $propietario->dni ?? null,
                    'fecha_apertura' => optional($historia->fecha_apertura)->format('d/m/Y'),
                ];
            })
            ->values();

        // Mascotas con sus relaciones para la tabla
        $mascotas = Mascota::with([
            'propietario',
            'historiaClinica.consultas',
        ])->paginate(10); // Paginación de 10 por página

        $today = Carbon::today();
        $endDate = $today->copy()->addDays(3);

        $upcomingAppointments = Cita::with(['historiaClinica.mascota.propietario'])
            ->where('estado', 'Pendiente')
            ->whereBetween('fecha_cita', [$today, $endDate])
            ->orderBy('fecha_cita')
            ->orderBy('hora_cita')
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

        return view('layouts.dashboard', compact(
            'totalMascotas',
            'totalPropietarios',
            'totalHistorias',
            'totalConsultas',
            'historias',
            'mascotas',
            'upcomingAppointments'
        ));
    }
}
