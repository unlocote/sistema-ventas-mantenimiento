@extends('layouts.app')

@section('title', 'Nueva Factura de Venta')

@section('content')
<div class="container">
    <h1>Nueva Factura de Venta</h1>

    {{-- ================== DATOS GENERALES ================== --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Datos Generales</div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="invoiceNumber" class="form-label">Número de factura</label>
                    <input type="text" id="invoiceNumber" class="form-control" value="FV-{{ time() }}" readonly>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Cliente</label>
                    <div class="input-group">
                        <input type="text" id="clienteNombre" class="form-control" placeholder="Seleccionar cliente..." readonly>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCliente">
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="col-md-3 text-end">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalProducto">+ Producto</button>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalServicio">+ Servicio</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================== DETALLES ================== --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">Detalles</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0" id="tablaItems">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Servicio</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <h4>Total: $<span id="totalFactura">0.00</span></h4>
        </div>
    </div>

    {{-- ================== ACCIONES ================== --}}
    <div class="text-end">
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar">Limpiar</button>
        <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
    </div>
</div>

{{-- ========================================================= --}}
{{-- =============== MODAL CLIENTE ================== --}}
{{-- ========================================================= --}}
<div class="modal fade" id="modalCliente" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Seleccionar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="buscarCliente" class="form-control mb-3" placeholder="Buscar por nombre...">
        <table class="table table-hover">
            <thead><tr><th>Nombre</th><th>Documento</th><th></th></tr></thead>
            <tbody id="tablaClientes"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ========================================================= --}}
{{-- =============== MODAL PRODUCTO ================== --}}
{{-- ========================================================= --}}
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Agregar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Producto</label>
            <select id="productoSelect" class="form-select">
                <option value="">Seleccione producto...</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Lote</label>
            <select id="loteSelect" class="form-select">
                <option value="">Seleccione lote...</option>
            </select>
        </div>

        <div id="mensajeMantenimiento" class="alert alert-warning d-none">
            Servicio de mantenimiento relacionado con este producto.
        </div>

        <div class="mb-3">
            <label class="form-label">Detalle de lote (opcional)</label>
            <select id="detalleLoteSelect" class="form-select">
                <option value="">Sin detalle</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" id="productoCantidad" class="form-control" min="1" value="1">
        </div>

        <div class="mb-3">
            <label class="form-label">Precio unitario</label>
            <input type="number" id="productoPrecio" class="form-control" min="0" step="0.01" value="120000">
        </div>

        <div class="text-end">
            <button type="button" id="btnGuardarProducto" class="btn btn-success">Agregar</button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ========================================================= --}}
{{-- =============== MODAL SERVICIO ================== --}}
{{-- ========================================================= --}}
<div class="modal fade" id="modalServicio" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Agregar Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Nombre del servicio</label>
            <input type="text" id="servicioNombre" class="form-control" placeholder="Ej: Mantenimiento de equipos">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de servicio</label>
            <select id="servicioTipo" class="form-select">
                <option value="">Seleccione tipo...</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" id="servicioCantidad" class="form-control" min="1" value="1" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Precio unitario</label>
            <input type="number" id="servicioPrecio" class="form-control" min="0" step="0.01" value="50000">
        </div>

        <div class="text-end">
            <button type="button" id="btnGuardarServicio" class="btn btn-info">Agregar</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const CLIENTES = [
        {id:1,nombre:"Acme S.A.",doc:"900123456-1"},
        {id:2,nombre:"Comercial López",doc:"800987654-2"},
        {id:3,nombre:"Juan Pérez",doc:"123456789"}
    ];

    const PRODUCTOS = [
        {id:1,nombre:"Router WiFi",precio:120000},
        {id:2,nombre:"Switch 8 puertos",precio:95000},
        {id:3,nombre:"Cámara IP",precio:210000}
    ];

    const LOTES = [
        {id:1,producto_id:1,nombre:"Lote A"},
        {id:2,producto_id:1,nombre:"Lote B"},
        {id:3,producto_id:2,nombre:"Lote C"},
        {id:4,producto_id:3,nombre:"Lote D"}
    ];

    const DETALLES = [
        {id:1,lote_id:1,serial:"R001"},
        {id:2,lote_id:1,serial:"R002"},
        {id:3,lote_id:3,serial:"S001"},
        {id:4,lote_id:4,serial:"C001"}
    ];

    const TIPOS_SERVICIO = ["Mantenimiento","Capacitación","Instalación"];

    const tabla = document.querySelector('#tablaItems tbody');
    const totalFactura = document.getElementById('totalFactura');
    const clienteInput = document.getElementById('clienteNombre');
    const tablaClientes = document.getElementById('tablaClientes');
    const buscarCliente = document.getElementById('buscarCliente');
    const mensajeMantenimiento = document.getElementById('mensajeMantenimiento');

    // ==== CLIENTES ====
    function renderClientes(filtro='') {
        tablaClientes.innerHTML = '';
        CLIENTES.filter(c => c.nombre.toLowerCase().includes(filtro.toLowerCase()))
        .forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${c.nombre}</td><td>${c.doc}</td><td><button class="btn btn-sm btn-primary">Seleccionar</button></td>`;
            tr.querySelector('button').addEventListener('click', () => {
                clienteInput.value = `${c.nombre} (${c.doc})`;
                bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            });
            tablaClientes.appendChild(tr);
        });
    }
    renderClientes();
    buscarCliente.addEventListener('input', e => renderClientes(e.target.value));

    // ==== PRODUCTOS ====
    const productoSelect = document.getElementById('productoSelect');
    const loteSelect = document.getElementById('loteSelect');
    const detalleLoteSelect = document.getElementById('detalleLoteSelect');
    const productoPrecio = document.getElementById('productoPrecio');

    PRODUCTOS.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.nombre} ($${p.precio.toLocaleString()})`;
        productoSelect.appendChild(opt);
    });

    productoSelect.addEventListener('change', () => {
        const productoId = parseInt(productoSelect.value);
        loteSelect.innerHTML = '<option value="">Seleccione lote...</option>';
        LOTES.filter(l => l.producto_id === productoId).forEach(l => {
            const o = document.createElement('option');
            o.value = l.id;
            o.textContent = l.nombre;
            loteSelect.appendChild(o);
        });
        mensajeMantenimiento.classList.add('d-none');

        const producto = PRODUCTOS.find(p => p.id === productoId);
        if(producto) productoPrecio.value = producto.precio;
    });

    loteSelect.addEventListener('change', () => {
        const loteId = parseInt(loteSelect.value);
        detalleLoteSelect.innerHTML = '<option value="">Sin detalle</option>';
        DETALLES.filter(d => d.lote_id === loteId).forEach(d => {
            const o = document.createElement('option');
            o.value = d.id;
            o.textContent = `Serial ${d.serial}`;
            detalleLoteSelect.appendChild(o);
        });
        if (loteId) mensajeMantenimiento.classList.remove('d-none');
        else mensajeMantenimiento.classList.add('d-none');
    });

    document.getElementById('btnGuardarProducto').addEventListener('click', () => {
        const prodId = productoSelect.value;
        const lote = loteSelect.options[loteSelect.selectedIndex]?.text || '';
        const detalle = detalleLoteSelect.options[detalleLoteSelect.selectedIndex]?.text || '';
        const cantidad = document.getElementById('productoCantidad').value;
        const precio = parseFloat(productoPrecio.value || 0);
        const producto = PRODUCTOS.find(p => p.id == prodId);
        if(!prodId || !cantidad || !precio) return alert('Complete todos los campos del producto.');
        agregarItem({ tipo:'Producto', producto:`${producto.nombre}${lote ? ' - '+lote : ''}${detalle ? ' ('+detalle+')' : ''}`, servicio:'', cantidad, precio });
        bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
    });

    // ==== SERVICIOS ====
    const servicioTipo = document.getElementById('servicioTipo');
    const servicioPrecio = document.getElementById('servicioPrecio');
    const servicioCantidad = document.getElementById('servicioCantidad');

    TIPOS_SERVICIO.forEach(t => {
        const o = document.createElement('option');
        o.value = t;
        o.textContent = t;
        servicioTipo.appendChild(o);
    });

    document.getElementById('btnGuardarServicio').addEventListener('click', () => {
        const nombre = document.getElementById('servicioNombre').value;
        const tipo = servicioTipo.value;
        const cantidad = parseInt(servicioCantidad.value);
        const precio = parseFloat(servicioPrecio.value || 0);
        if(!nombre || !tipo || !cantidad || !precio) return alert('Complete todos los campos del servicio.');
        agregarItem({ tipo:'Servicio', producto:'', servicio:`${nombre} (${tipo})`, cantidad, precio });
        bootstrap.Modal.getInstance(document.getElementById('modalServicio')).hide();
    });

    // ==== AGREGAR ITEM A TABLA ====
    const tablaBody = document.querySelector('#tablaItems tbody');
    function agregarItem({ tipo, producto, servicio, cantidad, precio }) {
        const subtotal = (cantidad * precio).toFixed(2);
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${tipo}</td><td>${producto}</td><td>${servicio}</td><td>${cantidad}</td><td>$${Number(precio).toFixed(2)}</td><td class="text-end">$${subtotal}</td><td class="text-center"><button class="btn btn-sm btn-danger">x</button></td>`;
        tr.querySelector('button').addEventListener('click', () => { tr.remove(); actualizarTotal(); });
        tablaBody.appendChild(tr);
        actualizarTotal();
    }

    function actualizarTotal() {
        let total = 0;
        tablaBody.querySelectorAll('tr').forEach(r => {
            const val = r.children[5]?.textContent.replace('$','') || '0';
            total += parseFloat(val);
        });
        totalFactura.textContent = total.toFixed(2);
    }

    document.getElementById('btnLimpiar').addEventListener('click', () => {
        if(confirm('¿Vaciar factura?')) {
            tablaBody.innerHTML='';
            totalFactura.textContent='0.00';
            clienteInput.value='';
        }
    });

    document.getElementById('btnGuardar').addEventListener('click', () => {
        alert('Factura simulada guardada.\n(No hay conexión real con la base de datos).');
    });

});
</script>
@endsection
