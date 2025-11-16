<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa los expedientes clínicos de cada mascota y centraliza relaciones con consultas y vacunas.
 */
class HistoriaClinica extends Model
{
    protected $table = 'historia_clinicas';
    protected $primaryKey = 'id_historia';
    protected $fillable = [
        'id_mascota',
        'numero_historia',
        'fecha_apertura',
        'peso',
        'temperatura',
        'frecuencia_cardiaca',
        'sintomas',
        'diagnostico',
        'tratamientos',
        'vacunas',
        'notas',
        'archivo',
        'created_by',
    ];

    protected $casts = [
        'fecha_apertura' => 'date',
    ];

    /**
     * Cada historia se asocia a una sola mascota.
     */
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    /**
     * Agrupa todas las consultas médicas registradas dentro del expediente.
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_historia', 'id_historia');
    }

    /**
     * Relaciona las vacunas aplicadas a la mascota que pertenece a esta historia.
     */
    public function vacunas()
    {
        return $this->hasMany(Vacuna::class, 'id_mascota', 'id_mascota');
    }
}
