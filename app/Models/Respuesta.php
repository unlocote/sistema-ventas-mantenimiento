<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pregunta;
use App\Models\OpcionRespuesta;
use App\Models\RespuestaEncuesta;

class Respuesta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_respuesta';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'answer',
        'pregunta_id',
        'opcion_respuesta_id',
        'respuesta_encuesta_id'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }    

    public function opcionRespuesta()
    {
        return $this->belongsTo(OpcionRespuesta::class, 'opcion_respuesta_id');
    }

    public function respuestaEncuesta()
    {
        return $this->belongsTo(RespuestaEncuesta::class, 'respuesta_encuesta_id');
    }

}
