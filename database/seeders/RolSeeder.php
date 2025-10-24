<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create([
            'name' => 'Vendedor',
            'description' => 'Vendedor',
        ]);

        Rol::create([
            'name' => 'Técnico',
            'description' => 'Técnico',
        ]);

        Rol::create([
            'name' => 'Administrador',
            'description' => 'Administrador',
        ]);

        Rol::create([
            'name' => 'Coordinador',
            'description' => 'Coordinador',
        ]);
    }
}
