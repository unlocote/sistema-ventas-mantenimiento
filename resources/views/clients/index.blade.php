@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de clientes</h1>

    <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3">Nuevo cliente</a>

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
            @forelse($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->tipoId->shortName ?? '—' }}</td>
                <td>{{ $client->identification }}</td>
                <td>{{ $client->email }}</td>
                <td>
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
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