@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Factura de Venta</h1>

    <form id="formFacturaVenta" action="{{ route('sales.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="invoiceNumber" class="form-label">Número de factura</label>
                <input type="text" name="invoiceNumber" id="invoiceNumber" class="form-control" value="{{ $nextInvoiceNumber }}" readonly>
            </div>
            <div class="col-md-4">
                <label for="invoiceCreatedAt" class="form-label">Fecha</label>
                <input type="date" name="invoiceCreatedAt" id="invoiceCreatedAt" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="vendedor" class="form-label">Vendedor</label>
                <input type="text" class="form-control" value="{{ $vendedor->name }}" readonly>
                <input type="hidden" name="vendedor_id" value="{{ $vendedor->id }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <div class="input-group">
                <input type="text" id="clienteNombre" class="form-control" placeholder="Seleccione un cliente" readonly required>
                <input type="hidden" name="cliente_id" id="clienteId">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">Buscar cliente</button>
            </div>
        </div>

        <hr>

        <h4>Ventas</h4>
        <table class="table table-bordered" id="tablaVentas">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Lote</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="button" id="btnAgregarVenta" class="btn btn-success mb-3">+ Agregar producto</button>

        <div class="d-flex justify-content-end">
            <h5>Total: $<span id="totalFactura">0</span></h5>
        </div>

        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
        </div>
    </form>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead><tr><th>Nombre</th><th>Documento</th><th>Acción</th></tr></thead>
                    <tbody>
                        @foreach($clientes as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->document }}</td>
                            <td><button type="button" class="btn btn-sm btn-success" onclick="seleccionarCliente({{ $c->id }}, '{{ $c->name }}')">Seleccionar</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead><tr><th>Nombre</th><th>Marca</th><th>Acción</th></tr></thead>
                    <tbody>
                        @foreach($productos as $p)
                        <tr>
                            <td>{{ $p->nombre }}</td>
                            <td>{{ $p->marca }}</td>
                            <td><button type="button" class="btn btn-sm btn-success" onclick="seleccionarProducto({{ $p->id }}, '{{ $p->nombre }}')">Seleccionar</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lote -->
<div class="modal fade" id="modalLote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Lote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Código</th><th>Cantidad</th><th>Marca</th><th>Cantidad a vender</th><th>Precio unitario</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lotes as $lote)
                        <tr data-producto-id="{{ $lote->producto_id }}">
                            <td>{{ $lote->codigo }}</td>
                            <td>{{ $lote->currentQtty }}</td>
                            <td>{{ $lote->brand }}</td>
                            <td><input type="number" class="form-control cantidadInput" min="1" max="{{ $lote->currentQtty }}" value="1"></td>
                            <td><input type="number" class="form-control precioInput" min="0" value="0"></td>
                            <td><button type="button" class="btn btn-primary btn-sm" onclick="agregarVenta({{ $lote->id }}, {{ $lote->producto_id }}, '{{ $lote->codigo }}', {{ $lote->currentQtty }}, this)">Agregar</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ventas = [];
    let productoSeleccionado = null;
    let productoNombre = '';
    const tablaVentas = document.querySelector('#tablaVentas tbody');
    const totalFactura = document.getElementById('totalFactura');

    document.getElementById('btnAgregarVenta').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('modalProducto')).show();
    });

    window.seleccionarCliente = function(id, nombre) {
        document.getElementById('clienteId').value = id;
        document.getElementById('clienteNombre').value = nombre;
        bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
    };

    window.seleccionarProducto = function(id, nombre) {
        productoSeleccionado = id;
        productoNombre = nombre;
        document.querySelectorAll('#modalLote tbody tr').forEach(tr => {
            if (tr.dataset.productoId == id) tr.style.display = '';
            else tr.style.display = 'none';
        });
        bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
        new bootstrap.Modal(document.getElementById('modalLote')).show();
    };

    window.agregarVenta = function(loteId, productoId, codigoLote, cantidadMax, btn) {
        const row = btn.closest('tr');
        const cantidad = parseInt(row.querySelector('.cantidadInput').value);
        const precio = parseFloat(row.querySelector('.precioInput').value);
        if (cantidad <= 0 || cantidad > cantidadMax || precio < 0) { alert('Cantidad o precio inválido'); return; }

        const subtotal = cantidad * precio;
        ventas.push({ productoId, productoNombre, loteId, codigoLote, cantidad, precio, subtotal });
        actualizarTabla();
        bootstrap.Modal.getInstance(document.getElementById('modalLote')).hide();
    };

    function actualizarTabla() {
        tablaVentas.innerHTML = '';
        let total = 0;
        ventas.forEach((v, i) => {
            total += v.subtotal;
            tablaVentas.innerHTML += `
                <tr>
                    <td>${v.productoNombre}</td>
                    <td>${v.codigoLote}</td>
                    <td>${v.cantidad}</td>
                    <td>${v.precio}</td>
                    <td>${v.subtotal}</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarVenta(${i})">🗑</button></td>
                </tr>`;
        });
        totalFactura.innerText = total.toLocaleString();
    }

    window.eliminarVenta = function(index) {
        ventas.splice(index, 1);
        actualizarTabla();
    };

    document.getElementById('formFacturaVenta').addEventListener('submit', function(e) {
        ventas.forEach((v, i) => {
            for (const key in v) {
                if (['productoNombre','codigoLote','subtotal'].includes(key)) continue;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `ventas[${i}][${key}]`;
                input.value = v[key];
                this.appendChild(input);
            }
        });
    });
});
</script>
@endsection
