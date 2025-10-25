@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producto</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input 
                type="text" 
                name="name" 
                class="form-control" 
                value="{{ old('name', $product->name) }}" 
                required
            >
            @error('name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="basePrice" class="form-label">Precio Base</label>
            <input 
                type="number" 
                step="0.01" 
                min="0" 
                name="basePrice" 
                class="form-control" 
                value="{{ old('basePrice', $product->basePrice) }}" 
                required
            >
            @error('basePrice')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea 
                id="description" 
                name="description" 
                rows="5" 
                class="form-control"
            >{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Imagen del Producto</label>
            <input type="file" name="image" class="form-control">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail mt-2" width="150">
            @endif
            @error('image')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>


        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection