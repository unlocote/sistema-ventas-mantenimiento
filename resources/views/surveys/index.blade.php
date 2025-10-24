@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Encuestas</h1>
        <a href="{{ route('surveys.create') }}" class="btn btn-success">
            ➕ Nueva encuesta
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($encuestas->isEmpty())
        <div class="alert alert-info">No hay encuestas registradas aún.</div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center" style="width: 120px;">Preguntas</th>
                            <th class="text-center" style="width: 280px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($encuestas as $index => $encuesta)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $encuesta->name }}</td>
                                <td>{{ Str::limit($encuesta->description, 80) }}</td>
                                <td class="text-center">
                                    {{ $encuesta->preguntas->count() }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('surveys.show', $encuesta->id) }}" 
                                       class="btn btn-sm btn-info text-white me-1">
                                        👁 Ver
                                    </a>
                                    <a href="{{ route('surveys.edit', $encuesta->id) }}" 
                                       class="btn btn-sm btn-primary me-1">
                                        ✏ Editar
                                    </a>
                                    <form action="{{ route('surveys.destroy', $encuesta->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Seguro que desea eliminar esta encuesta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            🗑 Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
