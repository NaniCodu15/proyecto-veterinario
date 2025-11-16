<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla `citas`. Representa los compromisos agendados por mascota.
 */
class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = false; // La tabla administra las fechas manualmente.

    protected $fillable = [
        'id_historia',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'estado',
    ];

    /**
     * Una cita pertenece a una única historia clínica que agrupa los datos de la mascota.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }
}
