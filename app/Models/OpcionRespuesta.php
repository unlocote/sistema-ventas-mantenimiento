<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpcionRespuesta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_opcion_respuesta';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = ['answer'];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }    

}
