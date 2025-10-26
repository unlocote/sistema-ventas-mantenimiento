@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Factura de Compra</h1>

    @if ($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
    @endif
    <form action="{{ route('purchases.store') }}" method="POST" id="formFacturaCompra">
        @csrf

        <!-- Información de la factura -->
        <div class="mb-3">
            <label for="invoiceNumber" class="form-label">Número de factura</label>
            <input type="text" name="invoiceNumber" class="form-control" value="{{ $nextInvoiceNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label for="invoiceCreatedAt" class="form-label">Fecha</label>
            <input type="date" name="invoiceCreatedAt" id="invoiceCreatedAt" class="form-control" 
                value="{{ old('invoiceCreatedAt', date('Y-m-d')) }}" required>
        </div>

        <!-- Proveedor -->
        <div class="mb-3">
            <label class="form-label">Proveedor</label>
            <div class="input-group">
                <input type="text" class="form-control" id="proveedorSelected" readonly>
                <input type="hidden" name="proveedor_id" id="proveedor_id">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedores">
                    Seleccionar Proveedor
                </button>
            </div>
        </div>

        <!-- Lista de Items (Lotes) -->
        <h4>Items de la factura</h4>
        <button type="button" class="btn btn-success mb-3" id="btnAgregarItem">
            Agregar Item
        </button>
        <table class="table" id="tableItems">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario de Compra</th>
                    <th>Precio Sugerido de Venta</th>
                    <th>Fecha de Expiracion</th>
                    <th>Subtotal</th>
                    <th>Detalles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>


        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Modal Proveedores -->
<div class="modal fade" id="modalProveedores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="buscarProveedor" class="form-control mb-3" placeholder="Buscar por nombre o documento">

                <table class="table" id="tableProveedores">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->identification }}</td>
                            <td>{{ $proveedor->name }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary seleccionarProveedor"
                                    data-id="{{ $proveedor->id }}"
                                    data-name="{{ $proveedor->name }}">
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

<!-- Modal Item -->
<div class="modal fade" id="modalItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar / Editar Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="itemIndex">

                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <input type="text" class="form-control" id="productoSelected" readonly>
                    <input type="hidden" id="producto_id">
                </div>

                <div class="mb-3">
                    <label class="form-label">Marca del Lote</label>
                    <input type="text" id="itemMarca" class="form-control" placeholder="Ej: Samsung">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de Expiración</label>
                    <input type="date" id="itemExpirationDate" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="itemCantidad" class="form-control" min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio Unitario de Compra</label>
                    <input type="number" id="itemPrecio" class="form-control" step="0.01" min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio Sugerido de Venta</label>
                    <input type="number" id="itemSuggestedRetailPrice" class="form-control" step="0.01" min="0" required>
                </div>

                <button type="button" class="btn btn-primary" id="guardarItem">Guardar Item</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Detalles de Lote -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Lote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table" id="tableDetalles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-primary" id="guardarDetalles">Guardar Detalles</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalProveedores = new bootstrap.Modal('#modalProveedores');
    const modalProductos = new bootstrap.Modal('#modalProductos');
    const modalItem = new bootstrap.Modal('#modalItem');
    const modalDetalles = new bootstrap.Modal('#modalDetalles');

    let lotes = [];

    function filterTable(inputId, tableId) {
        document.getElementById(inputId).addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }

    filterTable('buscarProveedor', 'tableProveedores');
    filterTable('buscarProducto', 'tableProductos');

    document.querySelectorAll('.seleccionarProveedor').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('proveedorSelected').value = this.dataset.name;
            document.getElementById('proveedor_id').value = this.dataset.id;
            modalProveedores.hide();
        });
    });

    document.getElementById('btnAgregarItem').addEventListener('click', function() {
        document.getElementById('productoSelected').value = '';
        document.getElementById('producto_id').value = '';
        document.getElementById('itemMarca').value = '';
        document.getElementById('itemCantidad').value = '';
        document.getElementById('itemPrecio').value = '';
        document.getElementById('itemSuggestedRetailPrice').value = '';
        document.getElementById('itemExpirationDate').value = '';
        document.getElementById('itemIndex').value = '';
        modalProductos.show();
    });

    document.querySelectorAll('.seleccionarProducto').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('productoSelected').value = this.dataset.name;
            document.getElementById('producto_id').value = this.dataset.id;
            modalProductos.hide();
            modalItem.show();
        });
    });

    document.getElementById('guardarItem').addEventListener('click', function() {
        const id = document.getElementById('producto_id').value;
        const name = document.getElementById('productoSelected').value;
        const marca = document.getElementById('itemMarca').value.trim();
        const cantidad = parseInt(document.getElementById('itemCantidad').value);
        const precio = parseFloat(document.getElementById('itemPrecio').value);
        const suggestedRetailPrice = parseFloat(document.getElementById('itemSuggestedRetailPrice').value);
        const expirationDate = document.getElementById('itemExpirationDate').value;
        const index = document.getElementById('itemIndex').value;

        if (!id || !cantidad || !precio || !marca || !suggestedRetailPrice) {
            alert('Debe completar todos los campos obligatorios (incluida la fecha de compra)');
            return;
        }

        const invoiceDate = document.getElementById('invoiceCreatedAt').value;
        if (expirationDate  && expirationDate < invoiceDate) {
            alert('La fecha de expiración no puede ser anterior a la fecha de la factura.');
            return;
        }

        const loteData = {
            producto_id: id,
            nombre: name,
            cantidad,
            buyPrice: precio,
            brand: marca,
            suggestedRetailPrice,
            expirationDate,
            detalles: []
        };

        if (index) {
            // Mantener los detalles existentes si los hay
            loteData.detalles = lotes[index].detalles || [];
            lotes[index] = { ...lotes[index], ...loteData };
        } else {
            lotes.push(loteData);
        }

        renderLotes();
        modalItem.hide();
    });

    function renderLotes() {
        const tbody = document.querySelector('#tableItems tbody');
        tbody.innerHTML = '';
        lotes.forEach((l, i) => {
            const tieneDetalles = l.detalles?.length > 0 
                ? `<span class="badge bg-success">Sí (${l.detalles.length})</span>` 
                : `<span class="badge bg-secondary">No</span>`;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${l.nombre}<input type="hidden" name="lotes[${i}][producto_id]" value="${l.producto_id}"></td>
                <td><input type="hidden" name="lotes[${i}][brand]" value="${l.brand}">${l.brand}</td>
                <td><input type="hidden" name="lotes[${i}][cantidad]" value="${l.cantidad}">${l.cantidad}</td>
                <td><input type="hidden" name="lotes[${i}][buyPrice]" value="${l.buyPrice}">${l.buyPrice}</td>
                <td><input type="hidden" name="lotes[${i}][suggestedRetailPrice]" value="${l.suggestedRetailPrice}">${l.suggestedRetailPrice}</td>
                <td><input type="hidden" name="lotes[${i}][expirationDate]" value="${l.expirationDate ?? ''}">${l.expirationDate ?? '-'}</td>
                <td><input type="hidden" name="lotes[${i}][subtotalPrice]" value="${l.buyPrice * l.cantidad}">${l.buyPrice * l.cantidad}</td>
                <td>${tieneDetalles}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-info detallesItem" data-index="${i}">Detalles</button>
                    <button type="button" class="btn btn-sm btn-outline-danger eliminarDetalles" data-index="${i}" ${l.detalles?.length > 0 ? '' : 'disabled'}>Eliminar Detalles</button>
                    <button type="button" class="btn btn-sm btn-warning editarItem" data-index="${i}">Editar Item</button>
                    <button type="button" class="btn btn-sm btn-danger eliminarItem" data-index="${i}">Eliminar Item</button>
                </td>`;
            tbody.appendChild(row);
        });
        actualizarBotones();
    }

    function actualizarBotones() {
        document.querySelectorAll('.editarItem').forEach(btn => {
            btn.onclick = () => {
                const i = btn.dataset.index;
                const l = lotes[i];
                document.getElementById('itemIndex').value = i;
                document.getElementById('productoSelected').value = l.nombre;
                document.getElementById('producto_id').value = l.producto_id;
                document.getElementById('itemMarca').value = l.brand;
                document.getElementById('itemCantidad').value = l.cantidad;
                document.getElementById('itemPrecio').value = l.buyPrice;
                document.getElementById('itemSuggestedRetailPrice').value = l.suggestedRetailPrice;
                document.getElementById('itemExpirationDate').value = l.expirationDate ?? '';
                modalItem.show();
            };
        });

        document.querySelectorAll('.eliminarItem').forEach(btn => {
            btn.onclick = () => {
                lotes.splice(btn.dataset.index, 1);
                renderLotes();
            };
        });

        document.querySelectorAll('.detallesItem').forEach(btn => {
            btn.onclick = () => abrirModalDetalles(btn.dataset.index);
        });

        document.querySelectorAll('.eliminarDetalles').forEach(btn => {
            btn.onclick = () => {
                const i = btn.dataset.index;
                if (!lotes[i].detalles || lotes[i].detalles.length === 0) {
                    alert('Este lote no tiene detalles para eliminar.');
                    return;
                }

                if (confirm('¿Está seguro que desea eliminar todos los detalles de este lote?')) {
                    lotes[i].detalles = [];
                    renderLotes();
                }
            };
        });
    }

    function abrirModalDetalles(index) {
        const lote = lotes[index];
        const tbody = document.querySelector('#tableDetalles tbody');
        tbody.innerHTML = '';
        if (!lote.detalles || lote.detalles.length !== lote.cantidad) {
            lote.detalles = Array.from({ length: lote.cantidad }, () => ({ serial: '' }));
        }
        lote.detalles.forEach((d, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td><input type="text" class="form-control detalleSerial" value="${d.serial}" data-index="${i}"></td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('guardarDetalles').onclick = () => {
            const seriales = [];
            let valido = true;
            tbody.querySelectorAll('tr').forEach((tr, i) => {
                const serial = tr.querySelector('.detalleSerial').value.trim();

                if (!serial) {
                    valido = false;
                    tr.querySelector('.detalleSerial').classList.add('is-invalid');
                } else {
                    tr.querySelector('.detalleSerial').classList.remove('is-invalid');
                }

                if (serial && seriales.includes(serial)) {
                    valido = false;
                    tr.querySelector('.detalleSerial').classList.add('is-invalid');
                } else if (serial) {
                    seriales.push(serial);
                }

                lote.detalles[i] = { serial };
            });

            if (!valido) {
                alert('Todos los seriales son obligatorios y no pueden repetirse.');
                return;
            }

            lotes[index] = lote;
            renderLotes();
            modalDetalles.hide();
        };

        modalDetalles.show();
    }

    // Antes de enviar el formulario, agregar los seriales como inputs ocultos
    document.getElementById('formFacturaCompra').addEventListener('submit', function () {
        // eliminar solo inputs de seriales antiguos, no tocar otros campos
        document.querySelectorAll('.input-detalle').forEach(el => el.remove());

        // agregar seriales como inputs ocultos
        lotes.forEach((l, i) => {
            if (l.detalles && l.detalles.length > 0) {
                l.detalles.forEach((d, j) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `lotes[${i}][detalles][${j}][serial]`;
                    input.value = d.serial;
                    input.classList.add('input-detalle');
                    this.appendChild(input);
                });
            }
        });

        // asegurarse de que proveedor_id siempre esté en el formulario
        const proveedorInput = document.getElementById('proveedor_id');
        if (!proveedorInput.value) {
            alert('Debe seleccionar un proveedor.');
            event.preventDefault();
            return false;
        }
    });

});
</script>
@endsection
