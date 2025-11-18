<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'id_consulta';

    /**
     * Campos que pueden completarse masivamente al registrar o editar una consulta médica.
     * Incluyen las referencias a la historia y los datos clínicos recopilados.
     */
    protected $fillable = [
        'id_historia',
        'fecha_consulta',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'peso',
        'temperatura',
    ];

    /**
     * Conversión automática de atributos a tipos nativos.
     * La fecha de consulta se castea a datetime para facilitar operaciones de fecha.
     */
    protected $casts = [
        'fecha_consulta' => 'datetime',
    ];

    /**
     * Relación: una consulta pertenece a una historia clínica.
     * Permite obtener la ficha médica relacionada mediante $consulta->historiaClinica.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

}
