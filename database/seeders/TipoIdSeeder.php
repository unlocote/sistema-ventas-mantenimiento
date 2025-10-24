<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoId;

class TipoIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoId::create([
            'name' => 'Cédula de ciudadanía',
            'shortName' => 'CC',
            'description' => 'Documento de identificación nacional para ciudadanos colombianos',
        ]);

        TipoId::create([
            'name' => 'Tarjeta de identidad',
            'shortName' => 'TI',
            'description' => 'Documento de identificación para menores de edad en Colombia',
        ]);

        TipoId::create([
            'name' => 'Cédula de extranjería',
            'shortName' => 'CE',
            'description' => 'Documento de identificación para extranjeros residentes en Colombia',
        ]);

        TipoId::create([
            'name' => 'Pasaporte',
            'shortName' => 'PA',
            'description' => 'Documento de viaje válido a nivel internacional',
        ]);

        TipoId::create([
            'name' => 'Registro civil de nacimiento',
            'shortName' => 'RC',
            'description' => 'Documento de identificación inicial para recién nacidos en Colombia',
        ]);
    }
}
