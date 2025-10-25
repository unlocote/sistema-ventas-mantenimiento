<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FacturaCompra extends Model
{
    const ESTADO_PENDIENTE = 0;
    const ESTADO_PAGADA = 1;
    const ESTADO_PAGO_PARCIAL = 2;

    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_factura_compra';

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
        'status',
        'proveedor_id',
        'empleado_id'
    ];

    // Convierte este campo automáticamente a Carbon
    protected $casts = [
        'invoiceCreatedAt' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($factura) {
            // Si no se asignó manualmente
            if (empty($factura->invoiceNumber)) {
                // Obtener el último número
                $lastFactura = self::orderBy('id', 'desc')->first();
                $nextNumber = $lastFactura ? ((int) filter_var($lastFactura->invoiceNumber, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;

                // Formatear con ceros (FC-00001)
                $factura->invoiceNumber = 'FC-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'factura_compra_id');
    }

}
