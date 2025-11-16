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

    /**
     * Campos asignables en masa para crear o actualizar citas.
     * Incluyen la historia clínica relacionada y los datos de programación y estado.
     */
    protected $fillable = [
        'id_historia',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'estado',
    ];

    /**
     * Relación: una cita pertenece a una historia clínica.
     * Permite acceder a la historia mediante $cita->historiaClinica.
     */
    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }
}
