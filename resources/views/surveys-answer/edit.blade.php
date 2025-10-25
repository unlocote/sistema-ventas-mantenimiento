@extends('layouts.app')

@section('title', 'Responder Encuesta')

@section('content')
<h1>Responder Encuesta: {{ $respuestaEncuesta->encuesta->name }}</h1>

<form method="POST" action="{{ route('surveys-answer.update', $respuestaEncuesta->id) }}" id="formEncuesta">
    @csrf
    @method('PUT')

    @foreach($respuestaEncuesta->encuesta->preguntas as $pregunta)
        <div class="mb-3">
            <label class="form-label"><strong>{{ $loop->iteration }}. {{ $pregunta->question }}</strong></label>
            @foreach($pregunta->opcionesRespuesta as $opcion)
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="respuestas[{{ $pregunta->id }}]"
                           id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                           value="{{ $opcion->id }}">
                    <label class="form-check-label" for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">
                        {{ $opcion->answer }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Enviar Respuestas</button>
</form>

@endsection

@section('scripts')
<script>
    const form = document.getElementById('formEncuesta');
    const btnSubmit = document.getElementById('btnSubmit');

    form.addEventListener('change', () => {
        // Comprobar si todas las preguntas tienen una opción seleccionada
        let todasRespondidas = true;
        @foreach($respuestaEncuesta->encuesta->preguntas as $pregunta)
            if (!form.querySelector('input[name="respuestas[{{ $pregunta->id }}]"]:checked')) {
                todasRespondidas = false;
            }
        @endforeach

        btnSubmit.disabled = !todasRespondidas;
    });
</script>
@endsection
