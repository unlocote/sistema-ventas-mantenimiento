<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proveedor;
use App\Models\TipoId;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $tipoId = TipoId::first();
        
        Proveedor::create([
            'identification' => '123456', 
            'name' => 'William Gualteros', 
            'phoneNumber'=> '3105551111', 
            'address'=> 'Calle XX # YY - ZZ', 
            'email'=> 'juanperez@gmail.com',
            'tipo_id'=> $tipoId->id
        ]);

    }
}
