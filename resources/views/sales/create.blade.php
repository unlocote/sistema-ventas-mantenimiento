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
                    <th>¿Equipos Seleccionados?</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="text-end mb-3">
            <h5><strong>Total factura: $<span id="totalFactura">0.00</span></strong></h5>
        </div>

        <input type="hidden" name="ventas" id="ventasInput">

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

{{-- ---------------- MODALES ---------------- --}}

{{-- Clientes --}}
<div class="modal fade" id="modalClientes" tabindex="-1" aria-hidden="true">
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

{{-- Productos --}}
<div class="modal fade" id="modalProductos" tabindex="-1" aria-hidden="true">
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

{{-- Lotes --}}
<div class="modal fade" id="modalLotes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Seleccionar Lote</h5></div>
            <div class="modal-body">
                <table class="table table-hover" id="tableLotes">
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th>Cantidad</th>
                            <th>Expira</th>
                            <th>Precio sugerido</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Item --}}
<div class="modal fade" id="modalItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Agregar Producto</h5></div>
            <div class="modal-body">
                <input type="hidden" id="producto_id">
                <input type="hidden" id="lote_id">

                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <input type="text" id="productoSelected" class="form-control" readonly>
                </div>

                <div id="infoLote" class="border p-2 mb-3 rounded" style="display:none">
                    <p><strong>Marca:</strong> <span id="infoMarca"></span></p>
                    <p><strong>Disponible:</strong> <span id="infoCantidad"></span></p>
                    <p><strong>Precio sugerido:</strong> <span id="infoSuggestedPrice"></span></p>
                    <p><strong>Expira:</strong> <span id="infoExpiracion"></span></p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="itemCantidad" class="form-control" min="1">
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

{{-- Servicio --}}
<div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Agregar Servicio</h5></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tipo de servicio</label>
                    <select id="tipoServicio" class="form-select">
                        @foreach($tiposServicio as $tipo)
                            <option value="{{ $tipo->id }}" data-price="{{ $tipo->price }}">
                                {{ $tipo->name }}
                            </option>
                        @endforeach
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

{{-- Equipos (DetalleLote) --}}
<div class="modal fade" id="modalEquipos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Seleccionar Equipos</h5></div>
            <div class="modal-body">
                <p class="text-muted">
                    Selecciona exactamente la cantidad de equipos correspondiente a este producto.
                </p>
                <table class="table table-hover" id="tableEquipos">
                    <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Marca</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <span id="equiposSeleccionadosInfo" class="me-auto text-muted"></span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarEquipos">Guardar selección</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalProductos = new bootstrap.Modal('#modalProductos');
    const modalLotes = new bootstrap.Modal('#modalLotes');
    const modalItem = new bootstrap.Modal('#modalItem');
    const modalServicio = new bootstrap.Modal('#modalServicio');

    const lotes = @json($lotes);
    let ventas = [];
    let productoSeleccionado = null;

    const tipoServicioSelect = document.getElementById('tipoServicio');
    const precioServicioInput = document.getElementById('precioServicio');

    document.getElementById('btnAgregarServicio').addEventListener('click', () => {
        const precioInicial = tipoServicioSelect.selectedOptions[0].dataset.price;
        precioServicioInput.value = parseFloat(precioInicial).toFixed(2);
        modalServicio.show();
    });

    tipoServicioSelect.addEventListener('change', function() {
        const precio = this.selectedOptions[0].dataset.price;
        precioServicioInput.value = parseFloat(precio).toFixed(2);
    });

    document.getElementById('btnAgregarProducto').addEventListener('click', () => modalProductos.show());

    document.querySelectorAll('.seleccionarCliente').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('clienteSelected').value = btn.dataset.name;
            document.getElementById('cliente_id').value = btn.dataset.id;
            bootstrap.Modal.getInstance(document.getElementById('modalClientes')).hide();
        };
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('seleccionarProducto')) {
            const id = parseInt(e.target.dataset.id);
            const nombre = e.target.dataset.name;
            productoSeleccionado = { id, nombre };

            const lotesFiltrados = lotes.filter(l =>
                l.producto_id === id &&
                l.currentQtty > 0 &&
                (!l.expirationDate || new Date(l.expirationDate) >= new Date())
            );

            const tbody = document.querySelector('#tableLotes tbody');
            tbody.innerHTML = '';
            lotesFiltrados.forEach(l => {
                const exp = l.expirationDate ?? '—';
                tbody.innerHTML += `
                    <tr>
                        <td>${l.brand ?? ''}</td>
                        <td>${l.currentQtty}</td>
                        <td>${exp}</td>
                        <td>${l.suggestedRetailPrice ?? 0}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary seleccionarLote"
                                data-id="${l.id}"
                                data-marca="${l.brand ?? ''}"
                                data-cantidad="${l.currentQtty}"
                                data-price="${l.suggestedRetailPrice ?? 0}"
                                data-expiracion="${exp}">Seleccionar</button>
                        </td>
                    </tr>`;
            });

            modalProductos.hide();
            modalLotes.show();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('seleccionarLote')) {
            const l = e.target.dataset;

            document.getElementById('lote_id').value = l.id;
            document.getElementById('producto_id').value = productoSeleccionado.id;
            document.getElementById('productoSelected').value = productoSeleccionado.nombre;

            document.getElementById('infoMarca').textContent = l.marca;
            document.getElementById('infoCantidad').textContent = l.cantidad;
            document.getElementById('infoSuggestedPrice').textContent = l.price;
            document.getElementById('infoExpiracion').textContent = l.expiracion;
            document.getElementById('infoLote').style.display = 'block';

            const cantidadInput = document.getElementById('itemCantidad');
            cantidadInput.max = l.cantidad;
            cantidadInput.value = 1;

            document.getElementById('itemPrecio').value = parseFloat(l.price).toFixed(2);

            modalLotes.hide();
            modalItem.show();
        }
    });

    document.getElementById('guardarItem').addEventListener('click', () => {
        const nombre = document.getElementById('productoSelected').value;
        const lote = document.getElementById('infoMarca').textContent;
        const cantidad = parseFloat(document.getElementById('itemCantidad').value);
        const precio = parseFloat(document.getElementById('itemPrecio').value);
        if (!cantidad || !precio) return alert('Complete cantidad y precio.');

        const loteId = document.getElementById('lote_id').value;
        const disponibles = detalleLotes.filter(d => Number(d.lote_id) === Number(loteId) && !d.detalle_venta_id);

        ventas.push({ 
            tipo: 'Producto',
            referencia: nombre, 
            lote, 
            lote_id: loteId, 
            cantidad, 
            precio,
            tieneEquipos: disponibles.length > 0,
            detalleLotes: [],
        });
        renderVentas();
        modalItem.hide();
    });

    document.getElementById('guardarServicio').addEventListener('click', () => {
        const tipoNombre = tipoServicioSelect.selectedOptions[0].text;
        const precio = parseFloat(precioServicioInput.value);
        if (!precio) return alert('Ingrese precio.');

        ventas.push({ 
            tipo: 'Servicio', 
            referencia: tipoNombre, 
            servicio_id: tipoServicioSelect.value, 
            cantidad: 1, 
            precio 
        });
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

            // Determinar valor de "Equipos seleccionados"
            let equiposSeleccionados = 'No aplica';
            if (v.tipo === 'Producto' && v.tieneEquipos) {
                equiposSeleccionados = v.detalleLotes && v.detalleLotes.length === parseInt(v.cantidad) ? 'Sí' : 'No';
            }

            tbody.innerHTML += `
                <tr>
                    <td>${v.tipo}</td>
                    <td>${v.referencia}</td>
                    <td>${v.cantidad}</td>
                    <td>${v.precio}</td>
                    <td>${subtotal}</td>
                    <td>${equiposSeleccionados}</td>
                    <td>
                        ${v.tipo === 'Producto'  && v.tieneEquipos ? `
                            <button type="button" class="btn btn-sm btn-outline-primary seleccionarEquipos" data-index="${i}" data-lote="${v.lote_id}">
                                Seleccionar Equipos
                            </button>
                        ` : ''}
                        <button type="button" class="btn btn-danger btn-sm eliminarItem" data-index="${i}">Eliminar</button>
                    </td>
                </tr>`;
        });

        document.getElementById('totalFactura').textContent = total.toFixed(2);

        // Deshabilitar o habilitar botón guardar según si hay items pendientes
        const botonGuardar = document.querySelector('#formFacturaVenta button[type="submit"]');
        const pendientes = ventas.some(v => v.tipo === 'Producto' && v.tieneEquipos && (!v.detalleLotes || v.detalleLotes.length !== parseInt(v.cantidad)));
        botonGuardar.disabled = pendientes;

        document.querySelectorAll('.eliminarItem').forEach(b => b.onclick = () => {
            ventas.splice(b.dataset.index, 1);
            renderVentas();
        });
    }

    // --- EQUIPOS (DetalleLote) ---
    const modalEquipos = new bootstrap.Modal('#modalEquipos');
    const tableEquiposBody = document.querySelector('#tableEquipos tbody');
    let detalleLotes = @json($detalleLotes ?? []); // vienen desde el controlador
    let indexEquiposActual = null;

    document.addEventListener('click', e => {
        if (e.target.classList.contains('seleccionarEquipos')) {
            const index = parseInt(e.target.dataset.index);
            const loteId = parseInt(e.target.dataset.lote);
            indexEquiposActual = index;

            // Filtrar los DetalleLote disponibles del lote
            const disponibles = detalleLotes.filter(d => Number(d.lote_id) === Number(loteId) && !d.detalle_venta_id);

            const item = ventas[index];
            tableEquiposBody.innerHTML = '';
            disponibles.forEach(dl => {
                // Verificar si ya está seleccionado
                const checked = item.detalleLotes?.includes(dl.id) ? 'checked' : '';
                tableEquiposBody.innerHTML += `
                    <tr>
                        <td>${dl.serial}</td>
                        <td>${dl.brand ?? ''}</td>
                        <td>
                            <input type="checkbox" class="checkEquipo" data-id="${dl.id}" ${checked}>
                        </td>
                    </tr>`;
            });

            // Mostrar conteo actualizado
            const seleccionados = item.detalleLotes?.length ?? 0;
            document.getElementById('equiposSeleccionadosInfo').textContent = `${seleccionados} de ${item.cantidad} seleccionados`;
            //document.getElementById('equiposSeleccionadosInfo').textContent = `0 de ${item.cantidad} seleccionados`;
            modalEquipos.show();
        }
    });

    // Controlar selección
    document.addEventListener('change', e => {
        if (e.target.classList.contains('checkEquipo')) {
            const item = ventas[indexEquiposActual];
            const seleccionados = document.querySelectorAll('.checkEquipo:checked');
            const max = parseInt(item.cantidad);
            if (seleccionados.length > max) {
                e.target.checked = false;
                return alert(`Solo puedes seleccionar ${max} equipos.`);
            }
            document.getElementById('equiposSeleccionadosInfo').textContent =
                `${seleccionados.length} de ${max} seleccionados`;
        }
    });

    // Guardar equipos seleccionados
    document.getElementById('guardarEquipos').addEventListener('click', () => {
        const seleccionados = Array.from(document.querySelectorAll('.checkEquipo:checked')).map(c => parseInt(c.dataset.id));
        const item = ventas[indexEquiposActual];
        if (seleccionados.length !== parseInt(item.cantidad)) {
            return alert(`Debes seleccionar exactamente ${item.cantidad} equipos.`);
        }

        // Guardamos los IDs seleccionados en el item
        item.detalleLotes = seleccionados;
        modalEquipos.hide();
        renderVentas(); // <--- actualizamos la tabla para mostrar "Sí" en Equipos seleccionados
    });

    document.getElementById('formFacturaVenta').addEventListener('submit', function(e) {
        const clienteId = document.getElementById('cliente_id').value;
        const fecha = document.getElementById('invoiceCreatedAt').value;
        const pendientes = ventas.some(v => 
            v.tipo === 'Producto' && 
            v.tieneEquipos && 
            (!v.detalleLotes || v.detalleLotes.length !== parseInt(v.cantidad))
        );

        if (!clienteId) {
            e.preventDefault();
            alert('Debes seleccionar un cliente antes de guardar la factura.');
            return;
        }

        if (!fecha) {
            e.preventDefault();
            alert('La fecha de la factura es obligatoria.');
            return;
        }

        if (pendientes) {
            e.preventDefault();
            alert('Hay productos que requieren seleccionar equipos antes de guardar la factura.');
            return;
        }

        document.getElementById('ventasInput').value = JSON.stringify(ventas);
    });

});
</script>
@endsection
