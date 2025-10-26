<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_tipo_servicio')->insert([
            [
                'name' => 'Mantenimiento Preventivo',
                'description' => 'Revisión y mantenimiento programado para prevenir fallos.',
                'price' => 150000.00,
            ],
            [
                'name' => 'Mantenimiento Correctivo',
                'description' => 'Reparación y restauración de equipos o sistemas con fallas.',
                'price' => 200000.00,
            ],
            [
                'name' => 'Soporte',
                'description' => 'Asistencia técnica y resolución de problemas operativos.',
                'price' => 80000.00,
            ],
            [
                'name' => 'Capacitación',
                'description' => 'Formación técnica o teórica para el personal o cliente.',
                'price' => 120000.00,
            ],
        ]);
    }
}
