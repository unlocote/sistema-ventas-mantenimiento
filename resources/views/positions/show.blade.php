@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del position</h1>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{ $position->name }}</h4>
            <p><strong>ID:</strong> {{ $position->id }}</p>
            <p><strong>Nombre del Cargo:</strong> {{ $position->name }}</p>
            <p><strong>Descripción:</strong> {{ $position->description }}</p>
            <div class="mt-4">
                <h5><strong>Roles asociados:</strong></h5>

                @if($position->roles->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($position->roles as $role)
                            <li class="list-group-item">
                                <span class="badge bg-primary">{{ $role->name }}</span>
                                <small class="text-muted">{{ $role->description ?? '' }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p><em>Este cargo no tiene roles asignados.</em></p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('positions.edit', $position) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('positions.index') }}" class="btn btn-secondary">Volver</a>

        <form action="{{ route('positions.destroy', $position) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar este position?')">
                Eliminar
            </button>
        </form>
    </div>
</div>
@endsection