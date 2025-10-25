<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Encuesta;
use App\Models\Pregunta;
use App\Models\OpcionRespuesta;
use App\Models\Cliente;
use App\Models\RespuestaEncuesta;

class EncuestaController extends Controller
{
    public function index()
    {
        $encuestas = Encuesta::with('preguntas.opcionesRespuesta')->get();
        return view('surveys.index', compact('encuestas'));
    }

    public function show($id)
    {
        $encuesta = Encuesta::with('preguntas.opcionesRespuesta')->findOrFail($id);
        return view('surveys.show', compact('encuesta'));
    }

    public function create()
    {
        return view('surveys.create');
    }

    public function store(Request $request)
    {
        // Validación completa
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:tbl_encuesta,name', // 👈 nombre único
            'description' => 'nullable|string',
            'preguntas' => 'required|array|min:2', // 👈 al menos 2 preguntas
            'preguntas.*.question' => 'required|string|max:255',
            'preguntas.*.opciones' => 'required|array|min:3', // 👈 al menos 3 opciones
            'preguntas.*.opciones.*' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre de la encuesta es obligatorio.',
            'name.unique' => 'Ya existe una encuesta con ese nombre.',
            'preguntas.required' => 'Debe agregar al menos dos preguntas.',
            'preguntas.min' => 'La encuesta debe tener al menos dos preguntas.',
            'preguntas.*.question.required' => 'Cada pregunta debe tener texto.',
            'preguntas.*.opciones.required' => 'Cada pregunta debe tener al menos tres opciones.',
            'preguntas.*.opciones.min' => 'Cada pregunta debe tener al menos tres opciones.',
            'preguntas.*.opciones.*.required' => 'Todas las opciones deben tener texto.',
        ]);


        // Validación lógica extra (por si llegan estructuras inconsistentes)
        if (count($data['preguntas']) < 2) {
            return back()->withErrors(['error' => 'Debe ingresar al menos dos preguntas.'])->withInput();
        }

        foreach ($data['preguntas'] as $index => $pregunta) {
            if (!isset($pregunta['opciones']) || count($pregunta['opciones']) < 3) {
                return back()->withErrors([
                    "error" => "La pregunta #" . ($index + 1) . " debe tener al menos tres opciones de respuesta."
                ])->withInput();
            }
        }

        // Crear la encuesta
        $encuesta = Encuesta::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Crear preguntas y opciones
        foreach ($data['preguntas'] as $preguntaData) {
            $pregunta = $encuesta->preguntas()->create([
                'question' => $preguntaData['question'],
            ]);

            foreach ($preguntaData['opciones'] as $opcion) {
                $pregunta->opcionesRespuesta()->create([
                    'answer' => $opcion,
                ]);
            }
        }

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Encuesta creada correctamente.');
    }



    public function edit($id)
    {
        $encuesta = Encuesta::with('preguntas.opcionesRespuesta')->findOrFail($id);
        return view('surveys.edit', compact('encuesta'));
    }


    public function update(Request $request, $id)
    {
        $encuesta = Encuesta::with('preguntas.opcionesRespuesta')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:tbl_encuesta,name,' . $encuesta->id,
            'description' => 'nullable|string',
            'preguntas' => 'required|array|min:2',
            'preguntas.*.question' => 'required|string|max:255',
            'preguntas.*.opciones' => 'required|array|min:3',
            'preguntas.*.opciones.*' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre de la encuesta es obligatorio.',
            'name.unique' => 'Ya existe una encuesta con ese nombre.',
            'preguntas.required' => 'Debe agregar al menos dos preguntas.',
            'preguntas.min' => 'La encuesta debe tener al menos dos preguntas.',
            'preguntas.*.question.required' => 'Cada pregunta debe tener texto.',
            'preguntas.*.opciones.required' => 'Cada pregunta debe tener al menos tres opciones.',
            'preguntas.*.opciones.min' => 'Cada pregunta debe tener al menos tres opciones.',
            'preguntas.*.opciones.*.required' => 'Todas las opciones deben tener texto.',
        ]);

        // Actualizar encuesta
        $encuesta->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Eliminar preguntas y opciones antiguas
        foreach ($encuesta->preguntas as $pregunta) {
            $pregunta->opcionesRespuesta()->delete();
            $pregunta->delete();
        }

        // Crear nuevamente las preguntas y opciones
        foreach ($data['preguntas'] as $preguntaData) {
            $pregunta = $encuesta->preguntas()->create([
                'question' => $preguntaData['question'],
            ]);

            foreach ($preguntaData['opciones'] as $opcion) {
                $pregunta->opcionesRespuesta()->create(['answer' => $opcion]);
            }
        }

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Encuesta actualizada correctamente.');
    }


    public function destroy($id)
    {
        $encuesta = Encuesta::findOrFail($id);

        // Recorremos las preguntas para borrar sus opciones primero
        foreach ($encuesta->preguntas as $pregunta) {
            $pregunta->opcionesRespuesta()->delete();
        }

        // Luego eliminamos las preguntas
        $encuesta->preguntas()->delete();

        // Finalmente eliminamos la encuesta
        $encuesta->delete();

        return redirect()->route('surveys.index')->with('success', 'Encuesta eliminada correctamente.');
    }

    public function assign() 
    {
        return view('surveys.assign', [
            'asignaciones' => RespuestaEncuesta::with(['cliente', 'encuesta'])->get(),
            'clientes' => Cliente::all(),
            'encuestas' => Encuesta::all(),
        ]);
    }

    public function storeAssign() 
    {
        return view('surveys.assign', [
            'asignaciones' => RespuestaEncuesta::with(['cliente', 'encuesta'])->get(),
            'clientes' => Cliente::all(),
            'encuestas' => Encuesta::all(),
        ]);
    }
}
