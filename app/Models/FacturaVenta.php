<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaVenta extends Model
{
    const ESTADO_PENDIENTE = 0;
    const ESTADO_PAGADA = 1;
    const ESTADO_PAGO_PARCIAL = 2;

    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_factura_venta';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'invoiceNumber',
        'invoiceCreatedAt',
        'status'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(Empleado::class, 'vendedor_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoFacturaVenta::class, 'factura_venta_id');
    }


}
