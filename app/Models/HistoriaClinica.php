<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    protected $table = 'historia_clinicas';
    protected $primaryKey = 'id_historia';
    /**
     * Campos asignables en masa que representan la ficha médica de la mascota.
     * Incluyen referencias, valores clínicos y metadatos de creación.
     */
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

    /**
     * Conversiones de atributos a tipos nativos.
     * Se castea la fecha de apertura a instancia de fecha para manipulación segura.
     */
    protected $casts = [
        'fecha_apertura' => 'date',
    ];

    /**
     * Relación: la historia clínica pertenece a una mascota.
     * Permite acceder al animal asociado mediante $historia->mascota.
     */
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    /**
     * Relación: una historia clínica puede tener muchas consultas.
     * Disponible con $historia->consultas para acceder a las atenciones registradas.
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_historia', 'id_historia');
    }

    /**
     * Relación: una historia clínica puede tener varias vacunas asociadas a través de la mascota.
     * Útil para consultar el calendario de inmunizaciones de la mascota.
     */
    public function vacunas()
    {
        return $this->hasMany(Vacuna::class, 'id_mascota', 'id_mascota');
    }
}
