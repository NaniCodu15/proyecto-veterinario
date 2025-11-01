<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mascota;
use App\Models\Propietario;
use App\Models\HistoriaClinica;
use App\Models\Consulta;
use App\Models\Vacuna;

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

        return view('dashboard', compact(
            'totalMascotas',
            'totalPropietarios',
            'totalHistorias',
            'totalConsultas',
            'totalVacunas',
            'mascotas',
        ));
    }
}
