@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nuevo Producto</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>


        <div class="mb-3">
            <label for="basePrice" class="form-label">Precio Base</label>
            <input type="number" step="0.01" min="0" name="basePrice" class="form-control" value="{{ old('basePrice') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea id="description" name="description" rows="5" class="form-control" value="{{ old('description') }}">{{ old('description') }}</textarea>
        </div>


        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection