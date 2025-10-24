<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_rol';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'description',
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;
}
