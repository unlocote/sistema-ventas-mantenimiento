<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\RespuestaEncuesta;
use App\Models\Respuesta;


class ResponderEncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cliente = Auth::guard('cliente')->user();

        // Obtener asignaciones de encuestas para este cliente
        $asignaciones = RespuestaEncuesta::with(['encuesta', 'respuestas'])
            ->where('cliente_id', $cliente->id)
            ->get();

        return view('surveys-answer.index', compact('asignaciones'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        // Buscar la asignación de encuesta, solo si pertenece al cliente
        $respuestaEncuesta = RespuestaEncuesta::with([
            'cliente',
            'encuesta.preguntas.opcionesRespuesta',
            'respuestas.opcionRespuesta' // ⚠ esto es clave
        ])
        ->where('id', $id)
        ->firstOrFail();

        // Asegurarnos de que la encuesta ya fue respondida
        if (!$respuestaEncuesta->answeredAt) {
            return redirect()->route('surveys-answer.index')
                            ->with('error', 'Esta encuesta aún no ha sido respondida.');
        }

        return view('surveys-answer.show', compact('respuestaEncuesta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Auth::guard('cliente')->user();

        $respuestaEncuesta = RespuestaEncuesta::with('encuesta.preguntas.opcionesRespuesta')
            ->where('id', $id)
            ->where('cliente_id', $cliente->id)
            ->firstOrFail();

        return view('surveys-answer.edit', compact('respuestaEncuesta'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Auth::guard('cliente')->user();

        $respuestaEncuesta = RespuestaEncuesta::with('encuesta.preguntas.opcionesRespuesta')
            ->where('id', $id)
            ->where('cliente_id', $cliente->id)
            ->firstOrFail();

        $validated = $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|integer|exists:tbl_opcion_respuesta,id',
            'comment' => 'nullable|string|max:500',
        ]);

        foreach ($respuestaEncuesta->encuesta->preguntas as $pregunta) {
            if (!isset($validated['respuestas'][$pregunta->id])) continue;

            Respuesta::create([
                'pregunta_id' => $pregunta->id,
                'opcion_respuesta_id' => $validated['respuestas'][$pregunta->id],
                'respuesta_encuesta_id' => $respuestaEncuesta->id,
                'answer' => $pregunta->opcionesRespuesta
                                ->firstWhere('id', $validated['respuestas'][$pregunta->id])->answer
            ]);
        }

        $respuestaEncuesta->answeredAt = now();
        $respuestaEncuesta->comment = $validated['comment'] ?? null;
        $respuestaEncuesta->save();

        return redirect()->route('surveys-answer.index')
            ->with('success', 'Encuesta respondida correctamente.');
    }

}
