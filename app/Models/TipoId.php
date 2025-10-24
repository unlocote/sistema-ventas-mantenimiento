<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoId extends Model
{
    
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_tipo_id';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'shortName',
        'description',
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;
}
