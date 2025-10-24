@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del provider</h1>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{ $provider->name }}</h4>

            <p><strong>ID:</strong> {{ $provider->id }}</p>
            <p><strong>Tipo de identificación:</strong> {{ $provider->tipoId->shortName ?? '—' }}</p>
            <p><strong>Número de identificación:</strong> {{ $provider->identification }}</p>
            <p><strong>Teléfono:</strong> {{ $provider->phoneNumber }}</p>
            <p><strong>Dirección:</strong> {{ $provider->address }}</p>
            <p><strong>Correo electrónico:</strong> {{ $provider->email }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('providers.edit', $provider) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('providers.index') }}" class="btn btn-secondary">Volver</a>

        <form action="{{ route('providers.destroy', $provider) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar este provider?')">
                Eliminar
            </button>
        </form>
    </div>
</div>
@endsection