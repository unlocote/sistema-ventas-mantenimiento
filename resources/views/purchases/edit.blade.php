@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Factura de Compra</h1>

    @if ($errors->has('error'))
        <div class="alert alert-danger">
            {{ $errors->first('error') }}
        </div>
    @endif

    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="formFacturaCompra">
        @csrf
        @method('PUT')

        <!-- Información de la factura -->
        <div class="mb-3">
            <label for="invoiceNumber" class="form-label">Número de factura</label>
            <input type="text" name="invoiceNumber" class="form-control" value="{{ $purchase->invoiceNumber }}" readonly>
        </div>

        <div class="mb-3">
            <label for="invoiceCreatedAt" class="form-label">Fecha</label>
            <input type="date" name="invoiceCreatedAt" id="invoiceCreatedAt" class="form-control" 
                value="{{ old('invoiceCreatedAt', \Carbon\Carbon::parse($purchase->invoiceCreatedAt)->format('Y-m-d')) }}" required>
        </div>

        <!-- Proveedor (no editable) -->
        <div class="mb-3">
            <label class="form-label">Proveedor</label>
            <input type="text" class="form-control" value="{{ $purchase->proveedor->name ?? '' }}" readonly>
            <input type="hidden" name="proveedor_id" value="{{ $purchase->proveedor_id }}">
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
                    <th>Subtotal</th>
                    <th>Fecha de Expiracion</th>
                    <th>Detalles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Actualizar Factura</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

{{-- MODALES --}}
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
                    <input type="text" id="itemMarca" class="form-control">
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
                    <input type="number" id="itemSuggestedRetailPrice" class="form-control" step="0.01" min="0">
                </div>
                <button type="button" class="btn btn-primary" id="guardarItem">Guardar Item</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
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
    const modalProductos = new bootstrap.Modal('#modalProductos');
    const modalItem = new bootstrap.Modal('#modalItem');
    const modalDetalles = new bootstrap.Modal('#modalDetalles');

    let lotes = [];

    // Cargar lotes existentes
    @foreach($purchase->lotes as $lote)
    lotes.push({
        producto_id: {{ $lote->producto_id }},
        nombre: '{{ $lote->producto->name }}',
        brand: '{{ $lote->brand }}',
        cantidad: {{ $lote->initialQtty }},
        buyPrice: {{ $lote->buyPrice }},
        suggestedRetailPrice: {{ $lote->suggestedRetailPrice ?? 0 }},
        expirationDate: '{{ $lote->expirationDate ?? '' }}',
        detalles: [
            @foreach($lote->detallesLote as $detalle)
            { serial: '{{ $detalle->serial }}' },
            @endforeach
        ]
    });
    @endforeach


    function abrirModalDetalles(index) {
        const lote = lotes[index];
        const tbody = document.querySelector('#tableDetalles tbody');
        tbody.innerHTML = '';

        // Inicializa detalles si no existen
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

        // Botón Guardar Detalles
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
            const modalDetalles = bootstrap.Modal.getInstance(document.getElementById('modalDetalles'));
            modalDetalles.hide();
        };

        const modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));
        modalDetalles.show();
    }

    // Funciones auxiliares
    function renderLotes() {
        const tbody = document.querySelector('#tableItems tbody');
        tbody.innerHTML = '';
        lotes.forEach((l, i) => {
            const detallesBadge = l.detalles.length 
                ? `<span class="badge bg-success">Sí (${l.detalles.length})</span>` 
                : `<span class="badge bg-secondary">No</span>`;
            tbody.innerHTML += `
                <tr>
                    <td>${l.nombre}<input type="hidden" name="lotes[${i}][producto_id]" value="${l.producto_id}"></td>
                    <td><input type="hidden" name="lotes[${i}][brand]" value="${l.brand}">${l.brand}</td>
                    <td><input type="hidden" name="lotes[${i}][cantidad]" value="${l.cantidad}">${l.cantidad}</td>
                    <td><input type="hidden" name="lotes[${i}][buyPrice]" value="${l.buyPrice}">${l.buyPrice}</td>
                    <td><input type="hidden" name="lotes[${i}][suggestedRetailPrice]" value="${l.suggestedRetailPrice}">${l.suggestedRetailPrice}</td>
                    <td>${(l.cantidad * l.buyPrice).toFixed(2)}</td>
                    <td><input type="hidden" name="lotes[${i}][expirationDate]" value="${l.expirationDate ?? ''}">${l.expirationDate ?? '-'}</td>
                    <td>${detallesBadge}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info detallesItem" data-index="${i}">Detalles</button>
                        <button type="button" class="btn btn-sm btn-outline-danger eliminarDetalles" data-index="${i}" ${l.detalles.length ? '' : 'disabled'}>Eliminar Detalles</button>
                        <button type="button" class="btn btn-sm btn-warning editarItem" data-index="${i}">Editar Item</button>
                        <button type="button" class="btn btn-sm btn-danger eliminarItem" data-index="${i}">Eliminar Item</button>
                    </td>
                </tr>`;
        });
        actualizarBotones();
    }

    function actualizarBotones() {
        document.querySelectorAll('.editarItem').forEach(btn => btn.onclick = () => editarItem(btn.dataset.index));
        document.querySelectorAll('.eliminarItem').forEach(btn => btn.onclick = () => { lotes.splice(btn.dataset.index,1); renderLotes(); });
        document.querySelectorAll('.detallesItem').forEach(btn => btn.onclick = () => abrirModalDetalles(btn.dataset.index));
        document.querySelectorAll('.eliminarDetalles').forEach(btn => btn.onclick = () => {
            const i = btn.dataset.index;
            if (lotes[i].detalles.length && confirm('¿Desea eliminar todos los detalles?')) {
                lotes[i].detalles = [];
                renderLotes();
            }
        });
    }

    function editarItem(i) {
        const l = lotes[i];
        document.getElementById('itemIndex').value = i;
        document.getElementById('productoSelected').value = l.nombre;
        document.getElementById('producto_id').value = l.producto_id;
        document.getElementById('itemMarca').value = l.brand;
        document.getElementById('itemCantidad').value = l.cantidad;
        document.getElementById('itemPrecio').value = l.buyPrice;
        document.getElementById('itemSuggestedRetailPrice').value = l.suggestedRetailPrice;
        document.getElementById('itemExpirationDate').value = l.expirationDate;
        modalItem.show();
    }

    document.getElementById('btnAgregarItem').addEventListener('click', () => {
        document.getElementById('itemIndex').value = '';
        document.getElementById('productoSelected').value = '';
        document.getElementById('producto_id').value = '';
        document.getElementById('itemMarca').value = '';
        document.getElementById('itemCantidad').value = '';
        document.getElementById('itemPrecio').value = '';
        document.getElementById('itemExpirationDate').value = '';
        modalProductos.show();
    });

    document.querySelectorAll('.seleccionarProducto').forEach(btn => btn.addEventListener('click', function(){
        document.getElementById('productoSelected').value = this.dataset.name;
        document.getElementById('producto_id').value = this.dataset.id;
        modalProductos.hide();
        modalItem.show();
    }));

    document.getElementById('guardarItem').addEventListener('click', () => {
        const i = document.getElementById('itemIndex').value;
        const l = {
            producto_id: document.getElementById('producto_id').value,
            nombre: document.getElementById('productoSelected').value,
            brand: document.getElementById('itemMarca').value,
            cantidad: parseInt(document.getElementById('itemCantidad').value),
            buyPrice: parseFloat(document.getElementById('itemPrecio').value),
            suggestedRetailPrice: parseFloat(document.getElementById('itemSuggestedRetailPrice').value),
            expirationDate: document.getElementById('itemExpirationDate').value,
            detalles: i !== '' ? lotes[i].detalles : []
        };
        if(i !== '') lotes[i] = l; else lotes.push(l);
        renderLotes();
        modalItem.hide();
    });

    renderLotes();

});
</script>
@endsection
