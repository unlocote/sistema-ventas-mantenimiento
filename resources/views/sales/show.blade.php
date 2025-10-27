@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Factura de Venta: {{ $factura->invoiceNumber }}</h1>

    <div class="mb-3">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Volver al listado</a>
    </div>

    {{-- Información de la factura --}}
    <div class="card mb-4">
        <div class="card-header">Detalles de la Factura</div>
        <div class="card-body">
            <p><strong>Cliente:</strong> {{ $factura->cliente->name ?? '—' }}</p>
            <p><strong>Vendedor:</strong> {{ $factura->vendedor->name ?? '—' }}</p>
            <p><strong>Fecha:</strong> {{ $factura->invoiceCreatedAt }}</p>
            <p><strong>Estado:</strong>
                @switch($factura->status)
                    @case(\App\Models\FacturaVenta::ESTADO_PENDIENTE)
                        Pendiente
                        @break
                    @case(\App\Models\FacturaVenta::ESTADO_PAGADA)
                        Pagada
                        @break
                    @case(\App\Models\FacturaVenta::ESTADO_PAGO_PARCIAL)
                        Pago parcial
                        @break
                @endswitch
            </p>
        </div>
    </div>

    {{-- Items vendidos --}}
    <div class="card mb-4">
        <div class="card-header">Items de la Factura</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Producto/Servicio</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factura->ventas as $venta)
                        @if($venta->servicio)
                            <tr>
                                <td>Servicio</td>
                                <td>{{ $venta->servicio->tipoServicio->name ?? '—' }}</td>
                                <td>—</td>
                                <td>1</td>
                                <td>{{ number_format($venta->sellPrice, 2) }}</td>
                                <td>{{ number_format($venta->sellPrice, 2) }}</td>
                            </tr>
                        @elseif($venta->lote)
                            <tr>
                                <td>Producto</td>
                                <td>{{ $venta->lote->producto->name ?? '—' }}</td>
                                <td>{{ $venta->lote->brand ?? '—' }}</td>
                                <td>{{ $venta->quantity }}</td>
                                <td>{{ number_format($venta->sellPrice, 2) }}</td>
                                <td>{{ number_format($venta->sellPrice * $venta->quantity, 2) }}</td>
                            </tr>
                        @endif

                        {{-- Detalle de cada detalleVenta si existe --}}
                        @if($venta->detallesVenta)
                            @foreach($venta->detallesVenta as $detalle)
                                <tr class="table-secondary">
                                    <td>Detalle Lote</td>
                                    <td>{{ $detalle->detalleLote->name ?? '—' }}</td>
                                    <td>{{ $detalle->detalleLote->lote->brand ?? '—' }}</td>
                                    <td>1</td>
                                    <td>{{ number_format($venta->sellPrice, 2) }}</td>
                                    <td>{{ number_format($venta->sellPrice, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagos --}}
    @if($factura->pagos->count() > 0)
        <div class="card mb-4">
            <div class="card-header">Pagos realizados</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factura->pagos as $pago)
                            <tr>
                                <td>{{ $pago->created_at }}</td>
                                <td>{{ number_format($pago->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
