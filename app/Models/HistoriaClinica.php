<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relación: la historia clínica pertenece a una mascota
    // Esto significa que podemos acceder a la mascota de esta historia usando $historia->mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
    }

    // Relación: una historia clínica puede tener muchas consultas
    // Esto significa que podemos obtener todas las consultas de esta historia usando $historia->consultas
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_historia', 'id_historia');
    }

    // Relación: una historia clínica puede tener varias vacunas asociadas a través de la mascota
    public function vacunas()
    {
        return $this->hasMany(Vacuna::class, 'id_mascota', 'id_mascota');
    }
}
