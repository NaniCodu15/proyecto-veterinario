<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoDato extends Model
{
    use HasFactory;

    protected $table = 'respaldo_datos';

    /**
     * Campos permitidos para asignación masiva que describen un respaldo generado.
     * Incluyen la fecha, nombre y ruta del archivo y su estado.
     */
    protected $fillable = [
        'fecha_respaldo',
        'nombre_archivo',
        'ruta_archivo',
        'estado',
    ];

    /**
     * Conversión automática de atributos para fechas de respaldo.
     */
    protected $casts = [
        'fecha_respaldo' => 'datetime',
    ];
}
