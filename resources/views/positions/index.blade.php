@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Cargos</h1>

    <a href="{{ route('positions.create') }}" class="btn btn-primary mb-3">Nuevo cargo</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Roles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $position)
            <tr>
                <td>{{ $position->id }}</td>
                <td>{{ $position->name }}</td>
                <td>{{ $position->description }}</td>
                <td>
                    @foreach($position->roles as $rol)
                        <span class="badge bg-primary">{{ $rol->name }}</span>
                    @endforeach
                    @if($position->roles->isEmpty())
                        <em>Sin roles</em>
                    @endif
                </td>
                <td>
                    <a href="{{ route('positions.show', $position) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('positions.edit', $position) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('positions.destroy', $position) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4">No hay proveedores registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection