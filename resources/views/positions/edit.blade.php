@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Cargo</h1>

    <form action="{{ route('positions.update', $position) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nombre del cargo --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Cargo</label>
            <input
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                id="name"
                name="name"
                value="{{ old('name', $position->name) }}"
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Descripción --}}
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea
                class="form-control @error('description') is-invalid @enderror"
                id="description"
                name="description"
            >{{ old('description', $position->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Roles asignados --}}
        <div class="mb-3">
            <label class="form-label">Roles asignados</label>
            <div>
                @foreach ($roles as $role)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->id }}"
                            id="role_{{ $role->id }}"
                            {{ in_array($role->id, old('roles', $position->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('roles')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botones --}}
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('positions.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
