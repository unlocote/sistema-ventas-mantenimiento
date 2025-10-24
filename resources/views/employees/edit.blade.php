@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Empleado</h1>

    <form action="{{ route('employees.update', $employee->id) }}" method="POST" id="formEmpleado" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                <option value="{{ $tipo->id }}" {{ old('tipo_id', $employee->tipo_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->shortName }} - {{ $tipo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="identification" class="form-label">Identificación</label>
                        <input type="text" name="identification" class="form-control"
                               value="{{ old('identification', $employee->identification) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phoneNumber" class="form-label">Número de Teléfono</label>
                        <input type="text" name="phoneNumber" class="form-control" value="{{ old('phoneNumber', $employee->phoneNumber) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Dirección</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $employee->address) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de usuario</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $employee->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña (dejar en blanco para mantener)</label>
                        <input type="password" name="password" id="password" class="form-control">
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
                            <th>Documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->contratos as $contrato)
                            <tr data-id="{{ $contrato->id }}"
                                data-cargo_id="{{ $contrato->cargo_id }}"
                                data-start_date="{{ $contrato->start_date }}"
                                data-end_date="{{ $contrato->end_date ?? '' }}"
                                data-document="{{ $contrato->document ?? '' }}">
                                <td>{{ $contrato->cargo->name ?? 'N/A' }}</td>
                                <td>{{ $contrato->start_date }}</td>
                                <td>{{ $contrato->end_date ?? '-' }}</td>
                                <td>
                                    @if ($contrato->document)
                                        {{-- link al método download del controlador --}}
                                        <a href="{{ route('contracts.download', $contrato->id) }}" class="btn btn-sm btn-outline-primary">Descargar</a>
                                    @else
                                        <span class="text-muted">Sin documento</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary me-1" onclick="editarContratoExistente({{ $contrato->id }})">Editar</button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="marcarEliminarContrato({{ $contrato->id }}, this)">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p class="text-muted" id="sinContratosMsg" style="{{ $employee->contratos->count() ? 'display:none' : '' }}">
                    No hay contratos registrados.
                </p>
            </div>
        </div>

        {{-- input para IDs de contratos a eliminar --}}
        <div id="contratosAEliminar"></div>

        <button type="submit" class="btn btn-success">Guardar cambios</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

{{-- MODAL PARA CONTRATO --}}
<div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="modalContratoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContratoLabel">Agregar / Editar contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="contrato_id" value="">

                <div class="mb-3">
                    <label for="cargo_id_modal" class="form-label">Cargo</label>
                    <select id="cargo_id_modal" class="form-select">
                        <option value="">Seleccione...</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date_modal" class="form-label">Fecha inicio</label>
                    <input type="date" id="start_date_modal" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="end_date_modal" class="form-label">Fecha fin</label>
                    <input type="date" id="end_date_modal" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="document_modal" class="form-label">Documento (PDF o imagen)</label>
                    <input type="file" class="form-control" id="document_modal" accept=".pdf,image/*" required>
                    <small id="archivoActual" class="text-muted d-block mt-1"></small>
                </div>

                {{-- si editas un contrato existente, mantendremos la ruta actual en este hidden --}}
                <input type="hidden" id="existing_document_modal" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarContrato">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalContrato');
    const modal = new bootstrap.Modal(modalEl);
    const btnGuardarContrato = document.getElementById('btnGuardarContrato');
    const archivoActual = document.getElementById('archivoActual');

    // referencias
    const tabla = document.querySelector('#tablaContratos tbody');
    const sinContratosMsg = document.getElementById('sinContratosMsg');
    const contratosAEliminar = document.getElementById('contratosAEliminar');

    function actualizarSinContratos() {
        sinContratosMsg.style.display = tabla.rows.length ? 'none' : 'block';
    }

    // Guardar contrato (agregar o actualizar fila en la tabla DOM)
    btnGuardarContrato.addEventListener('click', function () {
        const contratoId = document.getElementById('contrato_id').value; // si existe => edición de existente
        const cargoId = document.getElementById('cargo_id_modal').value;
        const cargoText = document.getElementById('cargo_id_modal').selectedOptions[0]?.text || '';
        const startDate = document.getElementById('start_date_modal').value;
        const endDate = document.getElementById('end_date_modal').value || '';
        const fileInput = document.getElementById('document_modal');
        const file = fileInput.files[0] || null;
        const existingDoc = document.getElementById('existing_document_modal').value || '';

        if (!cargoId || !startDate) {
            alert('Cargo y fecha de inicio obligatorios.');
            return;
        }

        // Si no hay archivo y tampoco existe documento anterior → error
        if (!file && !existingDoc) {
            alert('Debes adjuntar un documento del contrato.');
            return;
        }

        // Si edición de contrato existente: actualizar fila que tenga data-id igual
        if (contratoId) {
            const row = document.querySelector(`tr[data-id='${contratoId}']`);
            if (row) {
                row.setAttribute('data-cargo_id', cargoId);
                row.setAttribute('data-start_date', startDate);
                row.setAttribute('data-end_date', endDate);
                // Si hay nuevo archivo lo ponemos como texto en la celda; si no, dejamos el link existente
                const docCell = row.cells[3];
                if (file) {
                    docCell.innerHTML = file.name;
                } else if (existingDoc) {
                    // mantener enlace al download (si existía)
                    docCell.innerHTML = `<a href="${existingDoc}" target="_blank" class="btn btn-sm btn-outline-primary">Descargar</a>`;
                } else {
                    docCell.textContent = 'Sin documento';
                }
                // actualizar columnas visibles
                row.cells[0].textContent = cargoText;
                row.cells[1].textContent = startDate;
                row.cells[2].textContent = endDate || '-';

                // actualizar atributos data-document para que el backend pueda usar existing_document si no se sube nuevo archivo
                if (file) {
                    // file no es transferible al server en este momento; cuando el formulario se envíe, crearemos un input file oculto
                    // Agregamos/actualizamos input file oculto en el formulario principal
                    crearInputFileEnForm(file, contratoId);
                    row.setAttribute('data-document', ''); // será reemplazado por input file
                } else {
                    row.setAttribute('data-document', existingDoc);
                }
            }
            crearInputsHiddenParaContratoExistente(contratoId, cargoId, startDate, endDate);
        } else {
            // Agregar nueva fila (contrato sin id -> será tratado como nuevo en update)
            const newRow = tabla.insertRow();
            newRow.setAttribute('data-id', 'new-' + Date.now());
            newRow.setAttribute('data-cargo_id', cargoId);
            newRow.setAttribute('data-start_date', startDate);
            newRow.setAttribute('data-end_date', endDate);
            newRow.setAttribute('data-document', '');

            newRow.innerHTML = `
                <td>${cargoText}</td>
                <td>${startDate}</td>
                <td>${endDate || '-'}</td>
                <td>${file ? file.name : 'Sin documento'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary me-1" onclick="editarFilaNueva(this)">Editar</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="marcarEliminarFilaNueva(this)">Eliminar</button>
                </td>
            `;

            // si subieron archivo, crear input file oculto con index único para que Laravel lo reciba
            if (file) {
                crearInputFileEnForm(file, newRow.getAttribute('data-id'));
            }

            // crear inputs hidden para enviar los datos al server (contratos nuevos)
            crearInputsHiddenParaFila(newRow);
        }

        limpiarModal();
        modal.hide();
        actualizarSinContratos();
    });

    // helper: crear input file oculto en el form principal (usar DataTransfer)
    function crearInputFileEnForm(file, referenciaId) {
        // el navegador no permite crear un input[type=file] con archivos que no provengan del usuario directamente salvo usando DataTransfer
        // pero ya tenemos el File object (file) porque el usuario seleccionó. Creamos input file oculto y le asignamos files.
        const input = document.createElement('input');
        input.type = 'file';
        input.name = `contratos_files[${referenciaId}]`; // backend podrá buscar por este name
        input.classList.add('d-none');

        // asignar File a input
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        document.getElementById('formEmpleado').appendChild(input);
    }

    // helper: crear inputs hidden con datos del contrato (para nuevas filas)
    function crearInputsHiddenParaFila(row) {
        const id = row.getAttribute('data-id');
        const cargoId = row.getAttribute('data-cargo_id');
        const startDate = row.getAttribute('data-start_date');
        const endDate = row.getAttribute('data-end_date');

        // cargo_id
        const h1 = document.createElement('input');
        h1.type = 'hidden';
        h1.name = `contratos_nuevos[${id}][cargo_id]`;
        h1.value = cargoId;
        document.getElementById('formEmpleado').appendChild(h1);

        // start_date
        const h2 = document.createElement('input');
        h2.type = 'hidden';
        h2.name = `contratos_nuevos[${id}][start_date]`;
        h2.value = startDate;
        document.getElementById('formEmpleado').appendChild(h2);

        // end_date
        const h3 = document.createElement('input');
        h3.type = 'hidden';
        h3.name = `contratos_nuevos[${id}][end_date]`;
        h3.value = endDate;
        document.getElementById('formEmpleado').appendChild(h3);
    }

    function crearInputsHiddenParaContratoExistente(id, cargoId, startDate, endDate) {
        // Elimina previos (por si ya existían)
        document.querySelectorAll(`[name^="contratos_existentes[${id}]"]`).forEach(i => i.remove());

        const form = document.getElementById('formEmpleado');

        const h1 = document.createElement('input');
        h1.type = 'hidden';
        h1.name = `contratos_existentes[${id}][cargo_id]`;
        h1.value = cargoId;
        form.appendChild(h1);

        const h2 = document.createElement('input');
        h2.type = 'hidden';
        h2.name = `contratos_existentes[${id}][start_date]`;
        h2.value = startDate;
        form.appendChild(h2);

        const h3 = document.createElement('input');
        h3.type = 'hidden';
        h3.name = `contratos_existentes[${id}][end_date]`;
        h3.value = endDate;
        form.appendChild(h3);
    }

    // editar contrato existente: abre modal y carga valores desde data- attributes del <tr>
    window.editarContratoExistente = function (id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        if (!row) return alert('Fila no encontrada.');

        document.getElementById('contrato_id').value = id;
        document.getElementById('cargo_id_modal').value = row.dataset.cargo_id || '';
        document.getElementById('start_date_modal').value = row.dataset.start_date || '';
        document.getElementById('end_date_modal').value = row.dataset.end_date || '';
        const doc = row.dataset.document || '';
        document.getElementById('existing_document_modal').value = doc;

        // Mostrar nombre de archivo o enlace
        if (doc) {
            // si doc contiene ruta al storage (o ruta de descarga), muéstrala
            archivoActual.innerHTML = `Documento actual: <small>${doc}</small>`;
        } else {
            archivoActual.textContent = '';
        }

        modal.show();
    };

    // marcar contrato existente para eliminar: añade hidden inputs con el id y borra fila visualmente
    window.marcarEliminarContrato = function (id, btn) {
        if (!confirm('¿Desea eliminar este contrato?')) return;
        // crear input hidden con id para que el backend lo elimine
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'eliminar_contratos[]';
        input.value = id;
        contratosAEliminar.appendChild(input);

        // eliminar fila de la tabla
        const row = document.querySelector(`tr[data-id='${id}']`);
        if (row) row.remove();
        actualizarSinContratos();
    };

    // funciones para filas nuevas (sin id numérico, ID empieza con "new-")
    window.editarFilaNueva = function(btn) {
        const row = btn.closest('tr');
        // cargar modal con valores de la fila
        document.getElementById('contrato_id').value = row.getAttribute('data-id'); // new-...
        document.getElementById('cargo_id_modal').value = row.getAttribute('data-cargo_id') || '';
        document.getElementById('start_date_modal').value = row.getAttribute('data-start_date') || '';
        document.getElementById('end_date_modal').value = row.getAttribute('data-end_date') || '';
        document.getElementById('existing_document_modal').value = row.getAttribute('data-document') || '';
        archivoActual.textContent = row.cells[3]?.textContent || '';
        modal.show();
    };

    window.marcarEliminarFilaNueva = function(btn) {
        if (!confirm('¿Desea eliminar este contrato?')) return;
        const row = btn.closest('tr');
        // si tiene inputs hidden creados en el formulario principal (contratos_nuevos[...]) no hay que marcarlos para eliminar; simplemente quitar
        // eliminar cualquier input file asociado (name contratos_files[<id>])
        const id = row.getAttribute('data-id');
        // remover inputs hidden y file asociados si existen
        // inputs con name que empiezan por contratos_nuevos[<id>]
        Array.from(document.querySelectorAll(`[name^="contratos_nuevos[${id}]"]`)).forEach(i => i.remove());
        Array.from(document.querySelectorAll(`[name^="contratos_files[${id}]"]`)).forEach(i => i.remove());
        row.remove();
        actualizarSinContratos();
    };

    function limpiarModal() {
        document.getElementById('contrato_id').value = '';
        document.getElementById('cargo_id_modal').value = '';
        document.getElementById('start_date_modal').value = '';
        document.getElementById('end_date_modal').value = '';
        document.getElementById('document_modal').value = '';
        document.getElementById('existing_document_modal').value = '';
        archivoActual.textContent = '';
    }

    modalEl.addEventListener('hidden.bs.modal', limpiarModal);
    actualizarSinContratos();
});
</script>
@endsection
