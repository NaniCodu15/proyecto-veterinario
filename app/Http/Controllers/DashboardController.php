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

/**
 * Controlador responsable de agrupar la información general del sistema para la vista principal
 * del panel administrativo. No ejecuta lógica de negocio sino que prepara métricas, colecciones
 * y recordatorios que permiten monitorear la operación de la clínica veterinaria.
 */
class DashboardController extends Controller
{
    /**
     * Construye todas las métricas y colecciones necesarias para poblar la vista dashboard.
     *
     * El método reúne totales globales, arma una lista paginada de mascotas con sus relaciones
     * y calcula las próximas citas. La información se arma únicamente para lectura, por lo que
     * no modifica estado alguno en la base de datos.
     */
    public function index()
    {
        // Estadísticas generales de entidades registradas para los KPIs del tablero.
        $totalMascotas = Mascota::count();
        $totalPropietarios = Propietario::count();
        $totalHistorias = HistoriaClinica::count();
        $totalConsultas = Consulta::count();
        $totalVacunas = Vacuna::count();

        // Construcción de la tabla principal del tablero incluyendo relaciones para evitar N+1.
        $mascotas = Mascota::with([
            'propietario',
            'historiaClinica.consultas',
            'historiaClinica.vacunas'
        ])->paginate(10); // Se limita a 10 registros por página para mantener el tablero liviano.

        $today = Carbon::today();
        $endDate = $today->copy()->addDays(3);

        // Consulta anticipada de citas pendientes en los próximos tres días para alertar al usuario.
        $upcomingAppointments = Cita::with(['historiaClinica.mascota.propietario'])
            ->where('estado', 'Pendiente')
            ->whereBetween('fecha_cita', [$today, $endDate])
            ->orderBy('fecha_cita')
            ->orderBy('hora_cita')
            ->get()
            ->map(function ($cita) {
                // Al crear la colección preparada se protegen relaciones opcionales para evitar errores.
                $historia = $cita->historiaClinica;
                $mascota = $historia?->mascota;
                $propietario = $mascota?->propietario;

                // Se arma el nombre completo eliminando valores nulos o vacíos para evitar dobles espacios.
                $nombrePropietario = trim(collect([
                    $propietario?->nombres,
                    $propietario?->apellidos,
                ])->filter()->implode(' '));

                // Parsing de fecha y hora para mostrar tanto formato corto como detallado.
                $fecha = $cita->fecha_cita ? Carbon::parse($cita->fecha_cita) : null;
                $hora = $cita->hora_cita ? substr($cita->hora_cita, 0, 5) : null;

                // Estructura final que consume la vista para cada tarjeta de cita programada.
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

        // Entrega compacta de datos a la vista principal del dashboard.
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
