@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<div class="container">
    <h1 class="mb-4">Catálogo de Productos</h1>

    @if($products->isEmpty())
        <div class="alert alert-info">No hay productos disponibles en este momento.</div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($products as $product)
                <div class="col">
                    <div class="card h-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">
                                {{ Str::limit($product->description, 80, '...') }}
                            </p>
                            <p class="card-text"><strong>Precio:</strong> ${{ number_format($product->basePrice, 2) }}</p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
