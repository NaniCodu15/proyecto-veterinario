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

    /**
     * Campos que pueden asignarse en masa al crear o actualizar una mascota.
     * Incluyen datos básicos, fechas y referencia al propietario.
     */
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
     * Relación: una mascota pertenece a un propietario.
     * Permite acceder al dueño mediante $mascota->propietario.
     */
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'propietario_id', 'id_propietario');
    }

    /**
     * Relación: una mascota tiene una historia clínica asociada.
     * Se obtiene con $mascota->historiaClinica.
     */
    public function historiaClinica()
    {
        return $this->hasOne(HistoriaClinica::class, 'id_mascota', 'id_mascota');
    }

    /**
     * Relación auxiliar hacia la historia clínica por identificador.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function historia()
    {
    return $this->belongsTo(HistoriaClinica::class, 'id_historia', 'id_historia');
    }

}
