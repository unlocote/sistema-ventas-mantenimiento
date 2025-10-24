@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Producto</h1>

    <div class="mt-4">
        <p><strong>ID:</strong> {{ $product->id }}</p>
        <p><strong>Nombre:</strong> {{ $product->name }}</p>
        <p><strong>Precio Base:</strong> ${{ number_format($product->basePrice, 2) }}</p>
        <p><strong>Descripción:</strong></p>
        <p>{{ $product->description ?? 'Sin descripción disponible.' }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Editar</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
