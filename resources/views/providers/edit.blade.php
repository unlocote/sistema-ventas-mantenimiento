@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar provider</h1>

    <form action="{{ route('providers.update', $provider) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="tipo_id" class="form-label">Tipo de identificación</label>
            <select name="tipo_id" id="tipo_id" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($tiposId as $tipo)
                    <option value="{{ $tipo->id }}"
                        {{ old('tipo_id', $provider->tipo_id) == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->shortName }} - {{ $tipo->name }}
                    </option>
                @endforeach
            </select>
            @error('tipo_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="identification" class="form-label">Identificación</label>
            <input type="text" name="identification" class="form-control"
                   value="{{ old('identification', $provider->identification) }}" required>
            @error('identification')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $provider->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phoneNumber" class="form-label">Número de Teléfono</label>
            <input type="text" name="phoneNumber" class="form-control"
                   value="{{ old('phoneNumber', $provider->phoneNumber) }}" required>
            @error('phoneNumber')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" name="address" class="form-control"
                   value="{{ old('address', $provider->address) }}" required>
            @error('address')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $provider->email) }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('providers.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection