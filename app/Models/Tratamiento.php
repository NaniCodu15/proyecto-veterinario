<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamientos';
    protected $primaryKey = 'id_tratamiento';
    public $timestamps = false;

    /**
     * Campos permitidos para asignación masiva al registrar un tratamiento.
     * Incluyen la consulta de referencia y detalles de la medicación.
     */
    protected $fillable = [
        'id_consulta',
        'medicamento',
        'dosis',
        'duracion',
        'indicaciones',
    ];

    /**
     * Relación: un tratamiento pertenece a una consulta.
     * Permite acceder a la consulta con $tratamiento->consulta.
     */
    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
