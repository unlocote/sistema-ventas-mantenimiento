<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Empleado;
use App\Models\TipoId;


class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoId = TipoId::first();
        
        Empleado::create([
            'identification' => '654321', 
            'name' => 'Juan Martínez', 
            'phoneNumber'=> '3184444777', 
            'address'=> 'Calle Falsa 123', 
            'email'=> 'juanmartinez@gmail.com',
            'username'=> 'jmartinez',
            'passwordHash'=> Hash::make('admin123'),
            'tipo_id'=> $tipoId->id
        ]);


    }
}
