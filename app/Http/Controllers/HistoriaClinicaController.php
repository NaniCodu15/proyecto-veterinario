<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoriaClinicaController extends Controller
{
    // ✅ Listar historias para AJAX
    public function list()
    {
        $historias = HistoriaClinica::with('mascota')->latest()->paginate(10);
        return view('historia_clinicas._index', compact('historias'));
    }

    // ✅ Crear historia clínica (AJAX)
    public function store(Request $request)
    {
        $request->validate([
            'id_mascota' => 'required|integer|exists:mascotas,id',
            'peso' => 'nullable|numeric',
            'temperatura' => 'nullable|numeric',
        ]);

        $data = $request->all();
        $data['numero_historia'] = 'HC-' . date('Y') . '-' . time();
        $data['created_by'] = Auth::id();

        HistoriaClinica::create($data);

        return response()->json(['success' => true]);
    }

    // ✅ Obtener 1 registro (para editar)
    public function show($id)
    {
        $historia = HistoriaClinica::with('mascota')->findOrFail($id);
        return response()->json($historia);
    }

    // ✅ Actualizar historia clínica (AJAX)
    public function update(Request $request, $id)
    {
        $historia = HistoriaClinica::findOrFail($id);
        $historia->update($request->all());

        return response()->json(['success' => true]);
    }

    // ✅ Eliminar historia clínica (AJAX)
    public function destroy($id)
    {
        HistoriaClinica::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
