<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo simple que almacena metadatos de las copias de seguridad generadas.
 */
class RespaldoDato extends Model
{
    use HasFactory;

    protected $table = 'respaldo_datos';

    protected $fillable = [
        'fecha_respaldo',
        'nombre_archivo',
        'ruta_archivo',
        'estado',
    ];

    protected $casts = [
        'fecha_respaldo' => 'datetime',
    ];
}
