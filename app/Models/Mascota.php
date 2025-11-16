<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa a las mascotas registradas, incluyendo su vínculo con propietarios e historias.
 */
class Mascota extends Model
{
    use HasFactory;

    protected $table = 'mascotas';
    protected $primaryKey = 'id_mascota';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'sexo',
        'fecha_nacimiento',
        'color',
        'propietario_id',
        'fecha_registro'
    ];

    /**
     * Asociación principal con el propietario responsable.
     */
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'propietario_id', 'id_propietario');
    }

    /**
     * Relación uno-a-uno con la historia clínica derivada de la mascota.
     */
    public function historiaClinica()
    {
        return $this->hasOne(HistoriaClinica::class, 'id_mascota', 'id_mascota');
    }

    /**
     * Alias adicional para acceder a la historia clínica desde otros componentes heredados.
     */
    public function historia()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

}
