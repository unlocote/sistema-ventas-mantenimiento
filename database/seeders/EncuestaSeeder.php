<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Encuesta;
use App\Models\Pregunta;
use App\Models\OpcionRespuesta;

class EncuestaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encuesta = Encuesta::create([
            'name' => 'Encuesta 01', 
            'description'=> 'Encuesta de pruebas'
        ]);

        $pregunta1 = Pregunta::create([
            'question' => '¿Cuál es la capital de Perú?',
            'encuesta_id'  => $encuesta->id
        ]);

        $respuesta = OpcionRespuesta::create([
            'answer' => 'Lima',
            'pregunta_id'  => $pregunta1->id
        ]);

        $respuesta = OpcionRespuesta::create([
            'answer' => 'Machu Pichu',
            'pregunta_id'  => $pregunta1->id
        ]);

        $respuesta = OpcionRespuesta::create([
            'answer' => 'Cuidad Gótica',
            'pregunta_id'  => $pregunta1->id
        ]);
    }
}
