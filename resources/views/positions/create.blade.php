@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear nuevo Cargo</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('positions.store') }}" method="POST">
                @csrf

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Cargo</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" id="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Roles -->
                <div class="mb-3">
                    <label class="form-label">Asignar Roles <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('roles') is-invalid @enderror"
                                           type="checkbox"
                                           name="roles[]"
                                           id="role_{{ $role->id }}"
                                           value="{{ $role->id }}"
                                           {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('positions.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
