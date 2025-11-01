<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'id_consulta';
    public $timestamps = false;

    protected $fillable = [
        'id_historia',
        'fecha_consulta',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'observaciones',
    ];

    // Relación: una consulta pertenece a una historia clínica
    // Esto significa que podemos acceder a la historia clínica de esta consulta usando $consulta->historiaClinica
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

    // Relación: una consulta puede tener muchos tratamientos
    // Esto significa que podemos obtener todos los tratamientos asociados a esta consulta usando $consulta->tratamientos
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'id_consulta', 'id_consulta');
    }
}
