<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Persona extends Model
{
    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    protected $fillable = ['identification', 'name', 'phoneNumber', 'address', 'email', 'tipo_id'];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    //relación común 
    public function tipoId()
    {
        return $this->belongsTo(TipoId::class, 'tipo_id', 'id');
    }

}
