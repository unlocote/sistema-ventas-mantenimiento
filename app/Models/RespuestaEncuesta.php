<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Encuesta;
use App\Models\Respuesta;

class RespuestaEncuesta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_respuesta_encuesta';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'assignedAt',
        'answeredAt',
        'comment',
        'encuesta_id',
        'cliente_id'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }    

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'respuesta_encuesta_id');
    }
}
