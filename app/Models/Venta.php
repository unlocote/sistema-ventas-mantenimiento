<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_venta';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'sellTime',
        'quantity',
        'sellPrice',
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function facturaVenta()
    {
        return $this->belongsTo(FacturaVenta::class, 'factura_venta_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promocion_id');
    }

    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'detalle_venta_id');
    }

}
