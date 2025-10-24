<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_lote';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'buyDate',
        'expirationDate',
        'initialQtty',
        'buyPrice',
        'currentQtty',

    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function detalleLote()
    {
        return $this->belongsTo(DetalleLote::class, 'detalle_lote_id');
    }

}
