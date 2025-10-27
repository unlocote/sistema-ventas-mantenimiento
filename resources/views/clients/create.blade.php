@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Cliente</h1>

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="tipo_id" class="form-label">Tipo de identificación</label>
            <select name="tipo_id" id="tipo_id" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($tiposId as $tipo)
                    <option value="{{ $tipo->id }}"
                        {{ old('tipo_id', $client->tipo_id ?? '') == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->shortName }} - {{ $tipo->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="identification" class="form-label">Identificación</label>
            <input type="text" name="identification" class="form-control" value="{{ old('identification') }}" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="phoneNumber" class="form-label">Número de Teléfono</label>
            <input type="text" name="phoneNumber" class="form-control" value="{{ old('phoneNumber') }}" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>


        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection