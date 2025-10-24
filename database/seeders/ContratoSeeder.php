<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Cargo;


class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $cargo = Cargo::first();
        $empleado = Empleado::first();
        Contrato::create([
            'creation_date' => $faker->date(), 
            'start_date' => $faker->dateTimeBetween('-1 year', 'now'), 
            'end_date'=> $faker->dateTimeBetween('now', '+1 year'), 
            'document'=> 'contratos/BkRbkKp16h62pLvx6yJRZntauBkFYsI2M4sMIl2O.pdf', 
            'empleado_id'=> $empleado->id,
            'cargo_id'=> $cargo->id,
        ]);
    }
}
