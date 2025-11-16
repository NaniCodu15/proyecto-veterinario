<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de propietarios/clientes que agrupa los datos de contacto y su vínculo con mascotas.
 */
class Propietario extends Model
{
    use HasFactory;

    protected $table = 'propietarios';
    protected $primaryKey = 'id_propietario';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'direccion',
        'fecha_registro',
    ];

    /**
     * Un propietario puede registrar varias mascotas.
     */
    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'propietario_id', 'id_propietario');
    }
}
