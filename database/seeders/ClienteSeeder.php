<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Cliente;
use App\Models\TipoId;


class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoId = TipoId::first();
        
        Cliente::create([
            'identification' => '654321', 
            'name' => 'Variedades Pepito', 
            'phoneNumber'=> '3008884446', 
            'address'=> 'Calle AA # BB - CC', 
            'email'=> 'pepitovariedades@gmail.com',
            'username'=> 'pepito',
            'passwordHash'=> Hash::make('admin123'),
            'tipo_id'=> $tipoId->id
        ]);
    }
}
