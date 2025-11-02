<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HistoriaClinicaController extends Controller
{
    // ✅ Listar historias para AJAX
    public function list(Request $request)
    {
        $query = HistoriaClinica::with(['mascota.propietario'])
            ->orderByDesc('fecha_apertura');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_historia', 'like', "%{$search}%")
                    ->orWhereHas('mascota', function ($mascotaQuery) use ($search) {
                        $mascotaQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('especie', 'like', "%{$search}%");
                    });
            });
        }

        $historias = $query->paginate(8)->withQueryString();

        return view('historia_clinicas._index', compact('historias'));
    }

    // ✅ Crear historia clínica (AJAX)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mascota' => 'required|integer|exists:mascotas,id_mascota',
            'fecha_apertura' => 'nullable|date',
            'peso' => 'nullable|numeric|min:0|max:200',
            'temperatura' => 'nullable|numeric|min:0|max:60',
            'frecuencia_cardiaca' => 'nullable|string|max:50',
            'sintomas' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tratamientos' => 'nullable|string',
            'vacunas' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $nextNumber = (HistoriaClinica::max('id_historia') ?? 0) + 1;

        $validated['numero_historia'] = 'HC-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $validated['fecha_apertura'] = $validated['fecha_apertura']
            ? Carbon::parse($validated['fecha_apertura'])->toDateString()
            : Carbon::now()->toDateString();
        $validated['created_by'] = Auth::id();

        HistoriaClinica::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica creada correctamente.',
        ], 201);
    }

    // ✅ Obtener 1 registro (para editar)
    public function show($id)
    {
        $historia = HistoriaClinica::with('mascota.propietario')->findOrFail($id);
        return response()->json($historia);
    }

    // ✅ Actualizar historia clínica (AJAX)
    public function update(Request $request, $id)
    {
        $historia = HistoriaClinica::findOrFail($id);

        $validated = $request->validate([
            'id_mascota' => 'required|integer|exists:mascotas,id_mascota',
            'fecha_apertura' => 'required|date',
            'peso' => 'nullable|numeric|min:0|max:200',
            'temperatura' => 'nullable|numeric|min:0|max:60',
            'frecuencia_cardiaca' => 'nullable|string|max:50',
            'sintomas' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tratamientos' => 'nullable|string',
            'vacunas' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $historia->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica actualizada correctamente.',
        ]);
    }

    // ✅ Eliminar historia clínica (AJAX)
    public function destroy($id)
    {
        HistoriaClinica::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica eliminada correctamente.',
        ]);
    }
}
