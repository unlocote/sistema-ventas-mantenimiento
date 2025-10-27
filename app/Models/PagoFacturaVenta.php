<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoFacturaVenta extends Model
{
    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_pago_factura_venta';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'paidAt',
        'paidValue',
        'paymentMethod',
        'description',
        'factura_venta_id'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function facturaVenta()
    {
        return $this->belongsTo(FacturaVenta::class, 'factura_venta_id');
    }



}
