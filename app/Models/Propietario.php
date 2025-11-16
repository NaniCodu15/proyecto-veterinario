<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    use HasFactory;

    protected $table = 'propietarios';
    protected $primaryKey = 'id_propietario';
    public $timestamps = false;

    /**
     * Campos permitidos para asignación masiva al crear o editar propietarios.
     * Incluyen datos personales y de contacto.
     */
    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'direccion',
        'fecha_registro',
    ];

    /**
     * Relación: un propietario puede tener muchas mascotas.
     * Disponible mediante $propietario->mascotas.
     */
    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'propietario_id', 'id_propietario');
    }
}
