@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Facturas de Venta</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-success">
            + Nueva Factura de Venta
        </a>
    </div>

    {{-- 🔍 Filtro por nombre del cliente --}}
    <form method="GET" action="{{ route('sales.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input
                    type="text"
                    name="cliente"
                    class="form-control"
                    placeholder="Buscar por nombre del cliente..."
                    value="{{ request('cliente') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </div>
    </form>

    {{-- 📋 Tabla de facturas --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número de Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($facturas as $factura)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $factura->invoiceNumber }}</td>
                            <td>{{ \Carbon\Carbon::parse($factura->invoiceCreatedAt)->format('d/m/Y') }}</td>
                            <td>{{ $factura->cliente->name ?? '—' }}</td>
                            <td>{{ $factura->vendedor->name ?? '—' }}</td>
                            <td>
                                @switch($factura->status)
                                    @case(\App\Models\FacturaVenta::ESTADO_PENDIENTE)
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                        @break
                                    @case(\App\Models\FacturaVenta::ESTADO_PAGADA)
                                        <span class="badge bg-success">Pagada</span>
                                        @break
                                    @case(\App\Models\FacturaVenta::ESTADO_PAGO_PARCIAL)
                                        <span class="badge bg-info text-dark">Pago Parcial</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Desconocido</span>
                                @endswitch
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $factura->id) }}" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                                <a href="{{ route('sales.edit', $factura->id) }}" class="btn btn-sm btn-outline-warning">
                                    Editar
                                </a>

                                {{-- 🔹 Botón "Pagar" solo si no está pagada --}}
                                @if ($factura->status !== \App\Models\FacturaVenta::ESTADO_PAGADA)
                                    <button type="button" class="btn btn-sm btn-outline-success">
                                        Pagar
                                    </button>
                                @endif

                                <form action="{{ route('sales.destroy', $factura->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta factura?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No se encontraron facturas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- 📄 Paginación --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $facturas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
