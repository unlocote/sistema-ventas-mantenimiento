@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Facturas de Compra</h1>

    {{-- Filtro de búsqueda --}}
    <form method="GET" action="{{ route('purchases.index') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Buscar por proveedor..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('purchases.create') }}" class="btn btn-success">
                    + Nueva Factura
                </a>
            </div>
        </div>
    </form>

    {{-- Tabla de facturas --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Número de Factura</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->invoiceNumber }}</td>
                            <td>{{ $purchase->proveedor->name ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($purchase->invoiceCreatedAt)->format('d/m/Y') }}</td>
                            <td>
                                ${{ number_format($purchase->lotes->sum(fn($l) => $l->initialQtty * $l->buyPrice), 0, ',', '.') }}
                            </td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info">
                                    Ver
                                </a>
                                <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta factura?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No se encontraron facturas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-3">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
