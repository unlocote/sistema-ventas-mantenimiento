@extends('layouts.app')

@section('title', 'Mis Encuestas')

@section('content')
<h1>Mis Encuestas</h1>

@if($asignaciones->isEmpty())
    <div class="alert alert-info">
        No tienes encuestas asignadas.
    </div>
@else
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre de la Encuesta</th>
                <th>Fecha de Asignación</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asignaciones as $asignacion)
                <tr>
                    <td>{{ $asignacion->encuesta->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($asignacion->assignedAt)->format('d/m/Y') }}</td>
                    <td>
                        @if($asignacion->respuestas->isNotEmpty())
                            <span class="badge bg-success">Respondida</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        @if($asignacion->respuestas->isNotEmpty())
                            <a href="{{ route('surveys-answer.show', $asignacion->id) }}" class="btn btn-info btn-sm">
                                Ver Respuesta
                            </a>
                        @else
                            <a href="{{ route('surveys-answer.edit', $asignacion->id) }}" class="btn btn-primary btn-sm">
                                Responder
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection