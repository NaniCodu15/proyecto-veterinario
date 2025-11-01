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

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'direccion',
        'fecha_registro',
    ];

    // Relación: un propietario puede tener muchas mascotas
    // Podemos obtener todas sus mascotas usando $propietario->mascotas
    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'propietario_id', 'id_propietario');
    }
}
