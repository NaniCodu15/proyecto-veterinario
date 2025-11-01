<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Relación: una vacuna pertenece a una mascota
    // Podemos acceder a la mascota usando $vacuna->mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }
}
