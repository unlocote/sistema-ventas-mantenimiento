@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Factura de Venta</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales.store') }}" method="POST" id="formFacturaVenta">
        @csrf

        <div class="mb-3">
            <label for="invoiceNumber" class="form-label">Número de factura</label>
            <input type="text" name="invoiceNumber" class="form-control" value="{{ $nextInvoiceNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label for="invoiceCreatedAt" class="form-label">Fecha</label>
            <input type="date" name="invoiceCreatedAt" id="invoiceCreatedAt" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <input type="hidden" name="vendedor_id" value="{{ auth()->id() }}">
        <div class="mb-3">
            <label class="form-label">Vendedor</label>
            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <div class="input-group">
                <input type="text" class="form-control" id="clienteSelected" readonly>
                <input type="hidden" name="cliente_id" id="cliente_id">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalClientes">
                    Seleccionar Cliente
                </button>
            </div>
        </div>

        <h4>Items de la factura</h4>
        <div class="mb-3 d-flex gap-2">
            <button type="button" class="btn btn-success" id="btnAgregarProducto">Agregar Producto</button>
            <button type="button" class="btn btn-info" id="btnAgregarServicio">Agregar Servicio</button>
        </div>

        <table class="table" id="tableItems">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Referencia</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="text-end mb-3">
            <h5><strong>Total factura: $<span id="totalFactura">0.00</span></strong></h5>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

{{-- ---------------- MODAL CLIENTES ---------------- --}}
<div class="modal fade" id="modalClientes" tabindex="-1" aria-labelledby="modalClientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Seleccionar Cliente</h5></div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Acción</th></tr></thead>
                    <tbody>
                        @foreach($clientes as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->name }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm seleccionarCliente"
                                    data-id="{{ $c->id }}" data-name="{{ $c->name }}">Seleccionar</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ---------------- MODAL PRODUCTOS ---------------- --}}
<div class="modal fade" id="modalProductos" tabindex="-1" aria-labelledby="modalProductosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Seleccionar Producto</h5></div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Acción</th></tr></thead>
                    <tbody>
                        @foreach($productos as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm seleccionarProducto"
                                    data-id="{{ $p->id }}" data-name="{{ $p->name }}">Seleccionar</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ---------------- MODAL ITEM (PRODUCTO) ---------------- --}}
<div class="modal fade" id="modalItem" tabindex="-1" aria-labelledby="modalItemLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Agregar Producto</h5></div>
            <div class="modal-body">
                <input type="hidden" id="producto_id">

                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <input type="text" id="productoSelected" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="itemCantidad" class="form-control" min="1" value="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio unitario</label>
                    <input type="number" id="itemPrecio" class="form-control" min="0" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarItem">Guardar</button>
            </div>
        </div>
    </div>
</div>

{{-- ---------------- MODAL SERVICIO ---------------- --}}
<div class="modal fade" id="modalServicio" tabindex="-1" aria-labelledby="modalServicioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Agregar Servicio</h5></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tipo de servicio</label>
                    <select id="tipoServicio" class="form-select">
                        <option value="0">Mantenimiento preventivo</option>
                        <option value="1">Mantenimiento correctivo</option>
                        <option value="2">Soporte</option>
                        <option value="3">Capacitación</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" id="precioServicio" class="form-control" min="0" step="0.01">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarServicio">Guardar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalProductos = new bootstrap.Modal('#modalProductos');
    const modalItem = new bootstrap.Modal('#modalItem');
    const modalServicio = new bootstrap.Modal('#modalServicio');

    let ventas = [];
    let productoSeleccionado = null;

    document.getElementById('btnAgregarProducto').addEventListener('click', () => modalProductos.show());
    document.getElementById('btnAgregarServicio').addEventListener('click', () => modalServicio.show());

    document.querySelectorAll('.seleccionarCliente').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('clienteSelected').value = btn.dataset.name;
            document.getElementById('cliente_id').value = btn.dataset.id;
            bootstrap.Modal.getInstance(document.getElementById('modalClientes')).hide();
        };
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('seleccionarProducto')) {
            productoSeleccionado = {
                id: e.target.dataset.id,
                nombre: e.target.dataset.name
            };
            modalProductos.hide();
            modalItem.show();
            document.getElementById('productoSelected').value = productoSeleccionado.nombre;
        }
    });

    document.getElementById('guardarItem').addEventListener('click', () => {
        const nombre = document.getElementById('productoSelected').value;
        const cantidad = parseFloat(document.getElementById('itemCantidad').value);
        const precio = parseFloat(document.getElementById('itemPrecio').value);
        if (!cantidad || !precio) return alert('Complete cantidad y precio.');

        ventas.push({ tipo: 'Producto', referencia: nombre, cantidad, precio });
        renderVentas();
        modalItem.hide();
    });

    document.getElementById('guardarServicio').addEventListener('click', () => {
        const tipo = parseInt(document.getElementById('tipoServicio').value);
        const precio = parseFloat(document.getElementById('precioServicio').value);
        if (!precio) return alert('Ingrese precio.');

        const nombres = ['Mantenimiento preventivo','Mantenimiento correctivo','Soporte','Capacitación'];
        ventas.push({ tipo: 'Servicio', referencia: nombres[tipo], cantidad: 1, precio });
        renderVentas();
        modalServicio.hide();
    });

    function renderVentas() {
        const tbody = document.querySelector('#tableItems tbody');
        tbody.innerHTML = '';
        let total = 0;
        ventas.forEach((v, i) => {
            const subtotal = (v.cantidad * v.precio).toFixed(2);
            total += parseFloat(subtotal);
            tbody.innerHTML += `
                <tr>
                    <td>${v.tipo}</td>
                    <td>${v.referencia}</td>
                    <td>${v.cantidad}</td>
                    <td>${v.precio}</td>
                    <td>${subtotal}</td>
                    <td><button type="button" class="btn btn-danger btn-sm eliminarItem" data-index="${i}">Eliminar</button></td>
                </tr>`;
        });

        document.getElementById('totalFactura').textContent = total.toFixed(2);

        document.querySelectorAll('.eliminarItem').forEach(b => b.onclick = () => {
            ventas.splice(b.dataset.index, 1);
            renderVentas();
        });
    }
});
</script>
@endsection
