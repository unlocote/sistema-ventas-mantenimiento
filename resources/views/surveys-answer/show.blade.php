@extends('layouts.app')

@section('title', 'Encuesta Respondida')

@section('content')
<h1>Encuesta Respondida: {{ $respuestaEncuesta->encuesta->name }}</h1>

{{-- Comentario del cliente --}}
<div class="mb-3">
    <label class="form-label"><strong>Comentario del cliente:</strong></label>
    <p>{{ $respuestaEncuesta->comment ?? 'Sin comentario' }}</p>
</div>

{{-- Preguntas y respuestas --}}
@foreach($respuestaEncuesta->encuesta->preguntas as $pregunta)
    <div class="mb-3">
        <label class="form-label"><strong>{{ $loop->iteration }}. {{ $pregunta->question }}</strong></label>
        <ul class="list-group">
            @foreach($pregunta->opcionesRespuesta as $opcion)
                @php
                    // Obtener la respuesta del cliente para esta pregunta
                    $respuesta = $respuestaEncuesta->respuestas
                        ->firstWhere('pregunta_id', $pregunta->id);
                    $seleccionada = $respuesta ? $respuesta->opcion_respuesta_id : null;
                @endphp
                <li class="list-group-item @if($seleccionada == $opcion->id) list-group-item-success fw-bold @endif">
                    {{ $opcion->answer }}
                    @if($seleccionada == $opcion->id)
                        <span class="badge bg-success ms-2">Seleccionada</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

<a href="{{ route('surveys-answer.index') }}" class="btn btn-secondary">Volver</a>
@endsection
