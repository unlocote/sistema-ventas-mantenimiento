<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cargo;
use App\Models\Rol;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolAdmin = Rol::where('name', 'Administrador')->first();
        $rolCoordi  = Rol::where('name', 'Coordinador')->first();
        $cargoGerente = Cargo::create([
            'name' => 'Gerente',
            'description' => 'El más más'
        ]);
        $cargoGerente->roles()->sync([$rolAdmin->id, $rolCoordi->id]);
        //$cargoGerente->roles()->sync([$rolAdmin->id]);
        //$cargoGerente->roles()->sync([$rolCoordi->id]);
    }
}
