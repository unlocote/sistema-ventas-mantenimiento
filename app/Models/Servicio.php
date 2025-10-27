<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
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
        'estado',
        'scheduledTime',
        'executionTime',
        'descripcion', 
        'tipo_servicio_id',
        'cliente_id',
        'tecnico_id'
    ];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    // Nueva relación obligatoria con TipoServicio
    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class, 'tipo_servicio_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id');
    }


}
