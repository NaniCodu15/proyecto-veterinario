<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoDato extends Model
{
    use HasFactory;

    protected $table = 'respaldo_datos';

    protected $fillable = [
        'fecha_respaldo',
        'nombre_archivo',
        'ruta_archivo',
        'ruta_remota',
        'estado',
    ];

    protected $casts = [
        'fecha_respaldo' => 'datetime',
    ];
}
