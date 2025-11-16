<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para los tratamientos recetados durante una consulta.
 */
class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamientos';
    protected $primaryKey = 'id_tratamiento';
    public $timestamps = false;

    protected $fillable = [
        'id_consulta',
        'medicamento',
        'dosis',
        'duracion',
        'indicaciones',
    ];

    /**
     * Cada tratamiento pertenece a la consulta en la que fue indicado.
     */
    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
