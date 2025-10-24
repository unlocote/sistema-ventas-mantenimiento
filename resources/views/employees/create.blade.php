@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nuevo Empleado</h1>

    <form action="{{ route('employees.store') }}" method="POST" id="formEmpleado" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- DATOS DEL EMPLEADO --}}
        <div class="mb-4">
            <div class="card-header">Datos del empleado</div>
            <div class="card-body"> 
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tipo_id" class="form-label">Tipo de identificación</label>
                        <select name="tipo_id" id="tipo_id" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($tiposId as $tipo)
                                <option value="{{ $tipo->id }}"
                                    {{ old('tipo_id', $employee->tipo_id ?? '') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->shortName }} - {{ $tipo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="identification" class="form-label">Identificación</label>
                        <input type="text" name="identification" class="form-control" value="{{ old('identification') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phoneNumber" class="form-label">Número de Teléfono</label>
                        <input type="text" name="phoneNumber" class="form-control" value="{{ old('phoneNumber') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Dirección</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de usuario</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTRATOS --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Contratos del empleado</span>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalContrato">
                    Agregar contrato
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="tablaContratos">
                    <thead>
                        <tr>
                            <th>Cargo</th>
                            <th>Fecha inicio</th>
                            <th>Fecha fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <!-- Agrega este párrafo aquí -->
                <p class="text-muted" id="sinContratosMsg">No hay contratos agregados.</p>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


{{-- MODAL PARA CONTRATO --}}
<div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="modalContratoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContratoLabel">Agregar contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="cargo_id" class="form-label">Cargo</label>
                    <select id="cargo_id" class="form-select">
                        <option value="">Seleccione...</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Fecha inicio</label>
                    <input type="date" id="start_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Fecha fin</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="document" class="form-label">Documento (PDF o imagen)</label>
                    <input type="file" class="form-control" id="document" accept=".pdf,image/*">
                    <small id="archivoActual" class="text-muted d-block mt-1"></small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAgregarContrato">Agregar</button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- SCRIPT --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contratos = [];
    const tbody = document.querySelector('#tablaContratos tbody');
    const msg = document.getElementById('sinContratosMsg');
    const modalEl = document.getElementById('modalContrato');
    const modal = new bootstrap.Modal(modalEl);
    const btnAgregar = document.getElementById('btnAgregarContrato');
    const archivoActual = document.getElementById('archivoActual');
    const modalLabel = document.getElementById('modalContratoLabel');

    let editIndex = null;

    btnAgregar.addEventListener('click', () => {
        const cargoSelect = document.getElementById('cargo_id');
        const cargoId = cargoSelect.value;
        const cargoNombre = cargoSelect.selectedOptions[0]?.text;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const documentInput = document.getElementById('document');
        const documentFile = documentInput.files[0] || null;

        if (!cargoId || !startDate) {
            alert('El cargo y la fecha de inicio son obligatorios.');
            return;
        }

        const contrato = {
            cargo_id: cargoId,
            name: cargoNombre,
            start_date: startDate,
            end_date: endDate,
            creation_date: new Date().toISOString().split('T')[0],
            document: documentFile
        };

        if (editIndex !== null) {
            contratos[editIndex] = contrato;
            editIndex = null;
        } else {
            contratos.push(contrato);
        }

        actualizarTabla();
        limpiarModal();
        modal.hide();
    });

    function actualizarTabla() {
        tbody.innerHTML = '';
        msg.style.display = contratos.length === 0 ? 'block' : 'none';

        contratos.forEach((c, i) => {
            const fila = document.createElement('tr');

            // Celdas visibles
            fila.innerHTML = `
                <td>${c.name}</td>
                <td>${c.start_date}</td>
                <td>${c.end_date || '-'}</td>
                <td>${c.document ? c.document.name : 'Sin documento'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary me-1" onclick="editarContrato(${i})">Editar</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarContrato(${i})">Eliminar</button>
                </td>
            `;

            // Campos ocultos
            const cargoInput = document.createElement('input');
            cargoInput.type = 'hidden';
            cargoInput.name = `contratos[${i}][cargo_id]`;
            cargoInput.value = c.cargo_id;

            const startInput = document.createElement('input');
            startInput.type = 'hidden';
            startInput.name = `contratos[${i}][start_date]`;
            startInput.value = c.start_date;

            const endInput = document.createElement('input');
            endInput.type = 'hidden';
            endInput.name = `contratos[${i}][end_date]`;
            endInput.value = c.end_date;

            const creationInput = document.createElement('input');
            creationInput.type = 'hidden';
            creationInput.name = `contratos[${i}][creation_date]`;
            creationInput.value = c.creation_date;

            fila.appendChild(cargoInput);
            fila.appendChild(startInput);
            fila.appendChild(endInput);
            fila.appendChild(creationInput);

            // Archivo (input file real)
            if (c.document) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `contratos[${i}][document]`;
                fileInput.classList.add('d-none');
                // Asignar directamente el archivo seleccionado al input
                const dt = new DataTransfer();
                dt.items.add(c.document);
                fileInput.files = dt.files;
                fila.appendChild(fileInput);
            }

            tbody.appendChild(fila);
        });
    }

    window.eliminarContrato = function (i) {
        if (confirm('¿Desea eliminar este contrato?')) {
            contratos.splice(i, 1);
            actualizarTabla();
        }
    };

    window.editarContrato = function (i) {
        const c = contratos[i];
        editIndex = i;

        document.getElementById('cargo_id').value = c.cargo_id;
        document.getElementById('start_date').value = c.start_date;
        document.getElementById('end_date').value = c.end_date;
        archivoActual.textContent = c.document ? `Archivo actual: ${c.document.name}` : '';

        modalLabel.textContent = 'Editar contrato';
        btnAgregar.textContent = 'Actualizar';
        modal.show();
    };

    function limpiarModal() {
        document.getElementById('cargo_id').value = '';
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        document.getElementById('document').value = '';
        archivoActual.textContent = '';
        modalLabel.textContent = 'Agregar contrato';
        btnAgregar.textContent = 'Agregar';
    }

    modalEl.addEventListener('hidden.bs.modal', limpiarModal);
});
</script>
@endsection