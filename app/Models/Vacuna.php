<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para las aplicaciones de vacunas registradas en el sistema.
 */
class Vacuna extends Model
{
    use HasFactory;

    protected $table = 'vacunas';
    protected $primaryKey = 'id_vacuna';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'nombre_vacuna',
        'fecha_aplicacion',
        'fecha_proxima',
        'observaciones',
    ];

    /**
     * Cada aplicación se asocia a una mascota para mostrar su calendario de vacunación.
     */
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }
}
