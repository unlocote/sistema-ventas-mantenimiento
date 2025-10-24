<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_pregunta';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = ['question'];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }    

    public function opcionesRespuesta()
    {
        return $this->hasMany(OpcionRespuesta::class, 'pregunta_id');
    }
}
