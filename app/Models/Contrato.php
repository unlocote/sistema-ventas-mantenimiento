<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'tbl_contrato';

    protected $fillable = ['creation_date', 'start_date', 'end_date', 'document', 'cargo_id', 'empleado_id'];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
}
