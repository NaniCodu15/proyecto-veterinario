<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'estado',
    ];

    // Relación: una cita pertenece a una mascota
    // Podemos acceder a la mascota de la cita usando $cita->mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }
}
