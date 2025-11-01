<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Relación: una mascota pertenece a un propietario
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'propietario_id', 'id_propietario');
    }

    // Relación: una mascota tiene una historia clínica
    public function historiaClinica()
    {
        return $this->hasOne(HistoriaClinica::class, 'id_mascota', 'id_mascota');
    }

    // Relación: una mascota tiene una historia clínica
    public function historia()
    {
    return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

}
