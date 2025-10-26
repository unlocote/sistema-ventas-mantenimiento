@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Factura de Compra #{{ $purchase->invoiceNumber }}</h1>

    <div class="mb-4">
        <strong>Proveedor:</strong> {{ $purchase->proveedor->name ?? '—' }}<br>
        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($purchase->invoiceCreatedAt)->format('d/m/Y') }}<br>
        <strong>Total:</strong> ${{ number_format($purchase->lotes->sum(fn($l) => $l->initialQtty * $l->buyPrice), 0, ',', '.') }}
    </div>

    <h4>Items de la factura</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Precio Sugerido de Venta</th> <!-- Nueva columna -->
                <th>Subtotal</th>
                <th>Fecha de Expiración</th>
                <th>Detalles (Seriales)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->lotes as $index => $lote)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $lote->producto->name ?? '—' }}</td>
                    <td>{{ $lote->brand }}</td>
                    <td>{{ $lote->initialQtty }}</td>
                    <td>${{ number_format($lote->buyPrice, 0, ',', '.') }}</td>
                    <td>${{ number_format($lote->suggestedRetailPrice, 0, ',', '.') }}</td> <!-- Mostrar valor -->
                    <td>${{ number_format($lote->initialQtty * $lote->buyPrice, 0, ',', '.') }}</td>
                    <td>{{ $lote->expirationDate ?? '-' }}</td>
                    <td>
                        @if($lote->detallesLote->count() > 0)
                            @foreach($lote->detallesLote as $detalle)
                                <span class="badge bg-secondary me-1 mb-1">{{ $detalle->serial }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
</div>
@endsection
