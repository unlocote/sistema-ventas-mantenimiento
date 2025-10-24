@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de proveedores</h1>

    <a href="{{ route('providers.create') }}" class="btn btn-primary mb-3">Nuevo proveedor</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo Id</th>
                <th>Identificación</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($providers as $provider)
            <tr>
                <td>{{ $provider->id }}</td>
                <td>{{ $provider->name }}</td>
                <td>{{ $provider->tipoId->shortName ?? '—' }}</td>
                <td>{{ $provider->identification }}</td>
                <td>{{ $provider->email }}</td>
                <td>
                    <a href="{{ route('providers.show', $provider) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('providers.edit', $provider) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline">
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