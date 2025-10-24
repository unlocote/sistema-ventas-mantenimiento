<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'tbl_cargo';

    protected $fillable = ['name', 'description'];

    // Si no usas created_at y updated_at
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tbl_cargo_rol', 'cargo_id', 'rol_id');
    }

}
