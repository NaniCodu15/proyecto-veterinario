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

        $mascotasConHistoria = Mascota::has('historiaClinica')->count();
        $mascotasSinHistoria = Mascota::doesntHave('historiaClinica')->count();

        $mascotas = Mascota::orderBy('nombre')->get(['id_mascota', 'nombre', 'especie']);

        return view('dashboard', compact(
            'totalMascotas',
            'totalPropietarios',
            'totalHistorias',
            'totalConsultas',
            'totalVacunas',
            'mascotas',
            'mascotasConHistoria',
            'mascotasSinHistoria',
        ));
    }
}
