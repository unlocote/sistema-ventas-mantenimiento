<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleLote extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_detalle_lote';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'serial',
        'brand',
        'lote_id'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;


    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

}
