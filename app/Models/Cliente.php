<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_cliente';

    // Clave primaria (opcional si es "id")
    protected $primaryKey = 'id';

    // Si no quieres increment automático, puedes desactivar
    public $incrementing = true;

    // Si el campo es entero
    protected $keyType = 'int';

    // Si no usas created_at y updated_at
    public $timestamps = false;

    protected $fillable = [
        'identification',
        'name',
        'phoneNumber',
        'address',
        'email',
        'tipo_id',
        'username',
        'passwordHash',
    ];

    protected $hidden = ['passwordHash'];

    // Esto indica a Laravel que el campo de contraseña se llama "passwordHash"
    public function getAuthPassword()
    {
        return $this->passwordHash;
    }    


    public function tipoId()
    {
        return $this->belongsTo(TipoId::class, 'tipo_id', 'id');
    }


}
