<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Encuesta;
use App\Models\Pregunta;
use App\Models\OpcionRespuesta;
use App\Models\Cliente;
use App\Models\RespuestaEncuesta;

class AsignarEncuestaController extends Controller
{
    public function index()
    {
        return view('surveys-assign.index', [
            'asignaciones' => RespuestaEncuesta::with(['cliente', 'encuesta'])->get(),
            'clientes' => Cliente::all(),
            'encuestas' => Encuesta::all(),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:tbl_cliente,id',
            'encuesta_id' => 'required|exists:tbl_encuesta,id',
            'comment' => 'nullable|string|max:500',
        ]);

        RespuestaEncuesta::create([
            'cliente_id' => $data['cliente_id'],
            'encuesta_id' => $data['encuesta_id'],
            'comment' => $data['comment'] ?? null,
            'assignedAt' => now(),
            'answeredAt' => null,
        ]);

        return redirect()
            ->route('surveys-assign.index')
            ->with('success', 'Encuesta asignada correctamente al cliente.');
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:tbl_cliente,id',
            'encuesta_id' => 'required|exists:tbl_encuesta,id',
        ]);

        $asignacion = RespuestaEncuesta::findOrFail($id);

        // Solo permitir actualización si no fue respondida
        if ($asignacion->answeredAt) {
            return redirect()
                ->route('surveys-assign.index')
                ->with('error', 'No se puede modificar una asignación ya respondida.');
        }

        $asignacion->update([
            'cliente_id' => $data['cliente_id'],
            'encuesta_id' => $data['encuesta_id'],
        ]);

        return redirect()
            ->route('surveys-assign.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }



    public function destroy($id)
    {
        $asignacion = RespuestaEncuesta::findOrFail($id);

        if ($asignacion->answeredAt) {
            return redirect()
                ->route('surveys-assign.index')
                ->with('error', 'No se puede eliminar una asignación ya respondida.');
        }

        $asignacion->delete();

        return redirect()
            ->route('surveys-assign.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }


}
