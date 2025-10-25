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

        <!-- Información de la factura -->
        <div class="mb-3">
            <label for="invoiceNumber" class="form-label">Número de factura</label>
            <input type="text" name="invoiceNumber" class="form-control" value="{{ $nextInvoiceNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label for="invoiceCreatedAt" class="form-label">Fecha</label>
            <input type="date" name="invoiceCreatedAt" id="invoiceCreatedAt" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <!-- Vendedor asignado por sesión -->
        <input type="hidden" name="vendedor_id" value="{{ auth()->id() }}">
        <div class="mb-3">
            <label class="form-label">Vendedor</label>
            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>

        <!-- Cliente -->
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

        <!-- Lista de Items (Ventas) -->
        <h4>Items de la factura</h4>
        <button type="button" class="btn btn-success mb-3" id="btnAgregarItem">
            Agregar Item
        </button>
        <table class="table" id="tableItems">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Lote</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Modal Clientes -->
<div class="modal fade" id="modalClientes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="buscarCliente" class="form-control mb-3" placeholder="Buscar por nombre">

                <table class="table" id="tableClientes">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->name }}</td>
                            <td>{{ $cliente->identification }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary seleccionarCliente"
                                    data-id="{{ $cliente->id }}"
                                    data-name="{{ $cliente->name }}">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Modal Productos -->
<div class="modal fade" id="modalProductos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="buscarProducto" class="form-control mb-3" placeholder="Buscar por nombre">

                <table class="table" id="tableProductos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>{{ $producto->name }}</td>
                            <td>{{ $producto->description }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary seleccionarProducto"
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->name }}">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Modal Lotes -->
<div class="modal fade" id="modalLotes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Lote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table" id="tableLotes">
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th>Cantidad Disponible</th>
                            <th>Fecha Expiración</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Item -->
<div class="modal fade" id="modalItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="itemIndex">
                <input type="hidden" id="producto_id">
                <input type="hidden" id="lote_id">

                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <input type="text" class="form-control" id="productoSelected" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lote</label>
                    <input type="text" class="form-control" id="loteSelected" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="itemCantidad" class="form-control" min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio Unitario</label>
                    <input type="number" id="itemPrecio" class="form-control" step="0.01" min="0">
                </div>

                <button type="button" class="btn btn-primary" id="guardarItem">Guardar Item</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalClientes = new bootstrap.Modal('#modalClientes');
    const modalProductos = new bootstrap.Modal('#modalProductos');
    const modalLotes = new bootstrap.Modal('#modalLotes');
    const modalItem = new bootstrap.Modal('#modalItem');

    let ventas = [];

    function filterTable(inputId, tableId) {
        document.getElementById(inputId).addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }

    filterTable('buscarCliente', 'tableClientes');
    filterTable('buscarProducto', 'tableProductos');

    // Selección de cliente
    document.querySelectorAll('.seleccionarCliente').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('clienteSelected').value = this.dataset.name;
            document.getElementById('cliente_id').value = this.dataset.id;
            modalClientes.hide();
        });
    });

    // Agregar item
    document.getElementById('btnAgregarItem').addEventListener('click', function() {
        document.getElementById('productoSelected').value = '';
        document.getElementById('producto_id').value = '';
        document.getElementById('loteSelected').value = '';
        document.getElementById('lote_id').value = '';
        document.getElementById('itemCantidad').value = '';
        document.getElementById('itemPrecio').value = '';
        document.getElementById('itemIndex').value = '';
        modalProductos.show();
    });

    // Selección de producto
    document.querySelectorAll('.seleccionarProducto').forEach(btn => {
        btn.addEventListener('click', function() {
            const productoId = this.dataset.id;
            const productoName = this.dataset.name;
            document.getElementById('productoSelected').value = productoName;
            document.getElementById('producto_id').value = productoId;
            modalProductos.hide();

            // Cargar lotes disponibles para este producto
            const tbody = document.querySelector('#tableLotes tbody');
            tbody.innerHTML = '';
            @foreach($lotes as $lote)
                if ({{ $lote->producto_id }} == productoId && {{ $lote->currentQtty }} > 0 && '{{ $lote->expirationDate }}' >= '{{ date("Y-m-d") }}') {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>{{ $lote->brand }}</td>
                                    <td>{{ $lote->currentQtty }}</td>
                                    <td>{{ $lote->expirationDate }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary seleccionarLote"
                                            data-id="{{ $lote->id }}"
                                            data-brand="{{ $lote->brand }}"
                                            data-cantidad="{{ $lote->currentQtty }}">
                                            Seleccionar
                                        </button>
                                    </td>`;
                    tbody.appendChild(tr);
                }
            @endforeach

            modalLotes.show();
        });
    });

    // Selección de lote
    document.addEventListener('click', function(e){
        if(e.target && e.target.classList.contains('seleccionarLote')) {
            const btn = e.target;
            document.getElementById('loteSelected').value = btn.dataset.brand;
            document.getElementById('lote_id').value = btn.dataset.id;
            modalLotes.hide();
            modalItem.show();
        }
    });

    // Guardar item
    document.getElementById('guardarItem').addEventListener('click', function() {
        const index = document.getElementById('itemIndex').value;
        const productoId = document.getElementById('producto_id').value;
        const loteId = document.getElementById('lote_id').value;
        const productoName = document.getElementById('productoSelected').value;
        const loteName = document.getElementById('loteSelected').value;
        const cantidad = parseInt(document.getElementById('itemCantidad').value);
        const precio = parseFloat(document.getElementById('itemPrecio').value);

        if (!productoId || !loteId || !cantidad || !precio) {
            alert('Debe completar todos los campos');
            return;
        }

        const itemData = { productoId, productoName, loteId, loteName, cantidad, precio };

        if(index) {
            ventas[index] = itemData;
        } else {
            ventas.push(itemData);
        }

        renderVentas();
        modalItem.hide();
    });

    function renderVentas() {
        const tbody = document.querySelector('#tableItems tbody');
        tbody.innerHTML = '';
        ventas.forEach((v, i) => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${v.productoName}<input type="hidden" name="ventas[${i}][producto_id]" value="${v.productoId}"></td>
                             <td>${v.loteName}<input type="hidden" name="ventas[${i}][lote_id]" value="${v.loteId}"></td>
                             <td>${v.cantidad}<input type="hidden" name="ventas[${i}][quantity]" value="${v.cantidad}"></td>
                             <td>${v.precio}<input type="hidden" name="ventas[${i}][sellPrice]" value="${v.precio}"></td>
                             <td>${v.cantidad * v.precio}</td>
                             <td>
                                <button type="button" class="btn btn-sm btn-warning editarItem" data-index="${i}">Editar</button>
                                <button type="button" class="btn btn-sm btn-danger eliminarItem" data-index="${i}">Eliminar</button>
                             </td>`;
            tbody.appendChild(row);
        });

        document.querySelectorAll('.editarItem').forEach(btn => {
            btn.onclick = () => {
                const i = btn.dataset.index;
                const item = ventas[i];
                document.getElementById('itemIndex').value = i;
                document.getElementById('producto_id').value = item.productoId;
                document.getElementById('productoSelected').value = item.productoName;
                document.getElementById('lote_id').value = item.loteId;
                document.getElementById('loteSelected').value = item.loteName;
                document.getElementById('itemCantidad').value = item.cantidad;
                document.getElementById('itemPrecio').value = item.precio;
                modalItem.show();
            };
        });

        document.querySelectorAll('.eliminarItem').forEach(btn => {
            btn.onclick = () => {
                ventas.splice(btn.dataset.index, 1);
                renderVentas();
            };
        });
    }

});
</script>
@endsection
