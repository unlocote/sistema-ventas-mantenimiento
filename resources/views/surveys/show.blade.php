@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Encuesta: {{ $encuesta->name }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Descripción</h5>
            <p class="card-text">{{ $encuesta->description ?: 'Sin descripción.' }}</p>
        </div>
    </div>

    <h4 class="mb-3">Preguntas</h4>

    @forelse ($encuesta->preguntas as $i => $pregunta)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    {{ $i + 1 }}. {{ $pregunta->question }}
                </h5>

                @if ($pregunta->opcionesRespuesta->isNotEmpty())
                    <ul class="list-group list-group-flush mt-2">
                        @foreach ($pregunta->opcionesRespuesta as $opcion)
                            <li class="list-group-item">
                                {{ $opcion->answer }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mt-2">No hay opciones definidas para esta pregunta.</p>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Esta encuesta no tiene preguntas definidas.</p>
    @endforelse

    <div class="text-end mt-4">
        <a href="{{ route('surveys.index') }}" class="btn btn-secondary">⬅ Volver</a>
        <a href="{{ route('surveys.edit', $encuesta->id) }}" class="btn btn-primary">✏ Editar</a>
    </div>
</div>
@endsection
