@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del cliente</h1>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{ $client->name }}</h4>

            <p><strong>ID:</strong> {{ $client->id }}</p>
            <p><strong>Tipo de identificación:</strong> {{ $client->tipoId->shortName ?? '—' }}</p>
            <p><strong>Número de identificación:</strong> {{ $client->identification }}</p>
            <p><strong>Teléfono:</strong> {{ $client->phoneNumber }}</p>
            <p><strong>Dirección:</strong> {{ $client->address }}</p>
            <p><strong>Correo electrónico:</strong> {{ $client->email }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Volver</a>

        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar este client?')">
                Eliminar
            </button>
        </form>
    </div>
</div>
@endsection