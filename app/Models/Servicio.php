<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    const MANTENIMIENTO_PREVENTIVO = 0;
    const MANTENIMIENTO_CORRECTIVO = 1;
    const SOPORTE = 2;
    const CAPACITACION = 3;

    const ESTADO_CREADO = 0;
    const ESTADO_AGENDADO = 1;
    const ESTADO_REALIZADO = 2;
    const ESTADO_CANCELADO = 3;


    // Nombre de la tabla (opcional, solo si no sigue convención plural)
    protected $table = 'tbl_servicio';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'tipo',
        'estado',
        'scheduledTime',
        'executionTime'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }


    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'detalle_venta_id');
    }

}
