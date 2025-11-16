<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de las consultas médicas registradas dentro de cada historia clínica.
 */
class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'id_consulta';

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

    protected $casts = [
        'fecha_consulta' => 'datetime',
    ];

    /**
     * Cada consulta pertenece a una única historia clínica (relación muchos-a-uno).
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    /**
     * Relación uno-a-muchos con los tratamientos prescritos durante la consulta.
     */
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'id_consulta', 'id_consulta');
    }
}
