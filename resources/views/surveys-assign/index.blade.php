@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Asignación de Encuestas a Clientes</h2>

    {{-- Botón para abrir modal de nueva asignación --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAsignarEncuesta">
        + Nueva asignación
    </button>

    {{-- Tabla de asignaciones --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Encuesta</th>
                <th>Fecha asignación</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asignaciones as $asignacion)
                <tr>
                    <td>{{ $asignacion->id }}</td>
                    <td>{{ $asignacion->cliente->name ?? '—' }}</td>
                    <td>{{ $asignacion->encuesta->name ?? '—' }}</td>
                    <td>{{ $asignacion->assignedAt ? \Carbon\Carbon::parse($asignacion->assignedAt)->format('d/m/Y H:i') : '—' }}</td>
                    <td>
                        @if($asignacion->answeredAt)
                            <span class="badge bg-success">Respondida</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!$asignacion->answeredAt)
                            {{-- Botón editar --}}
                            <button class="btn btn-sm btn-outline-primary btn-editar me-1"
                                data-id="{{ $asignacion->id }}"
                                data-cliente="{{ $asignacion->cliente->id }}"
                                data-cliente-nombre="{{ $asignacion->cliente->name }}"
                                data-encuesta="{{ $asignacion->encuesta->id }}"
                                data-encuesta-nombre="{{ $asignacion->encuesta->name }}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>

                            {{-- Botón eliminar --}}
                            <form action="{{ route('surveys-assign.destroy', $asignacion->id) }}" method="POST" class="d-inline form-eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="bi bi-lock-fill"></i> Bloqueada
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay asignaciones registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL: Asignar / Editar encuesta --}}
<div class="modal fade" id="modalAsignarEncuesta" tabindex="-1" aria-labelledby="modalAsignarEncuestaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formAsignarEncuesta" action="{{ route('surveys-assign.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="asignacion_id" name="asignacion_id">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAsignarEncuestaLabel">Asignar Encuesta a Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                {{-- Cliente --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Cliente</label>
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <input type="text" id="buscarCliente" class="form-control mb-2" placeholder="Buscar cliente por nombre o ID...">
                    <div class="border rounded" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0">
                            <tbody id="listaClientes">
                                @foreach($clientes as $cliente)
                                    <tr class="fila-cliente" data-id="{{ $cliente->id }}" style="cursor: pointer;">
                                        <td width="80">{{ $cliente->id }}</td>
                                        <td>{{ $cliente->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small id="clienteSeleccionado" class="text-muted fst-italic"></small>
                </div>

                {{-- Encuesta --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Encuesta</label>
                    <input type="hidden" name="encuesta_id" id="encuesta_id">
                    <input type="text" id="buscarEncuesta" class="form-control mb-2" placeholder="Buscar encuesta por nombre...">
                    <div class="border rounded" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-hover table-sm mb-0">
                            <tbody id="listaEncuestas">
                                @foreach($encuestas as $encuesta)
                                    <tr class="fila-encuesta" data-id="{{ $encuesta->id }}" style="cursor: pointer;">
                                        <td width="80">{{ $encuesta->id }}</td>
                                        <td>{{ $encuesta->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small id="encuestaSeleccionada" class="text-muted fst-italic"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarAsignacion">Asignar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalAsignarEncuesta'));
    const form = document.getElementById('formAsignarEncuesta');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalAsignarEncuestaLabel');
    const btnGuardar = document.getElementById('btnGuardarAsignacion');
    const asignacionId = document.getElementById('asignacion_id');

    // --- BUSCAR CLIENTE ---
    const buscarCliente = document.getElementById('buscarCliente');
    const listaClientes = document.getElementById('listaClientes');
    const clienteIdInput = document.getElementById('cliente_id');
    const clienteSeleccionado = document.getElementById('clienteSeleccionado');

    buscarCliente.addEventListener('input', () => {
        const filtro = buscarCliente.value.toLowerCase();
        listaClientes.querySelectorAll('tr').forEach(tr => {
            const texto = tr.innerText.toLowerCase();
            tr.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });

    listaClientes.addEventListener('click', e => {
        const fila = e.target.closest('tr');
        if (!fila) return;
        const id = fila.dataset.id;
        const nombre = fila.children[1].innerText;
        clienteIdInput.value = id;
        clienteSeleccionado.textContent = `Seleccionado: ${id} - ${nombre}`;
        listaClientes.querySelectorAll('tr').forEach(tr => tr.classList.remove('table-primary'));
        fila.classList.add('table-primary');
    });

    // --- BUSCAR ENCUESTA ---
    const buscarEncuesta = document.getElementById('buscarEncuesta');
    const listaEncuestas = document.getElementById('listaEncuestas');
    const encuestaIdInput = document.getElementById('encuesta_id');
    const encuestaSeleccionada = document.getElementById('encuestaSeleccionada');

    buscarEncuesta.addEventListener('input', () => {
        const filtro = buscarEncuesta.value.toLowerCase();
        listaEncuestas.querySelectorAll('tr').forEach(tr => {
            const texto = tr.innerText.toLowerCase();
            tr.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });

    listaEncuestas.addEventListener('click', e => {
        const fila = e.target.closest('tr');
        if (!fila) return;
        const id = fila.dataset.id;
        const nombre = fila.children[1].innerText;
        encuestaIdInput.value = id;
        encuestaSeleccionada.textContent = `Seleccionada: ${id} - ${nombre}`;
        listaEncuestas.querySelectorAll('tr').forEach(tr => tr.classList.remove('table-primary'));
        fila.classList.add('table-primary');
    });

    // --- CONFIRMAR ELIMINACIÓN ---
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            if (confirm('¿Seguro que deseas eliminar esta asignación?')) {
                form.submit();
            }
        });
    });

    // --- EDITAR ASIGNACIÓN ---
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const clienteId = btn.dataset.cliente;
            const clienteNombre = btn.dataset.clienteNombre;
            const encuestaId = btn.dataset.encuesta;
            const encuestaNombre = btn.dataset.encuestaNombre;

            // Actualizar formulario
            form.action = `/surveys-assign/${id}`;
            formMethod.value = 'PUT';
            asignacionId.value = id;

            clienteIdInput.value = clienteId;
            clienteSeleccionado.textContent = `Seleccionado: ${clienteId} - ${clienteNombre}`;
            encuestaIdInput.value = encuestaId;
            encuestaSeleccionada.textContent = `Seleccionada: ${encuestaId} - ${encuestaNombre}`;

            modalTitle.textContent = 'Editar Asignación';
            btnGuardar.textContent = 'Guardar cambios';
            modal.show();
        });
    });

    // --- NUEVA ASIGNACIÓN ---
    document.querySelector('[data-bs-target="#modalAsignarEncuesta"]').addEventListener('click', () => {
        form.reset();
        form.action = '{{ route('surveys-assign.store') }}';
        formMethod.value = 'POST';
        asignacionId.value = '';
        clienteSeleccionado.textContent = '';
        encuestaSeleccionada.textContent = '';
        modalTitle.textContent = 'Asignar Encuesta a Cliente';
        btnGuardar.textContent = 'Asignar';
    });
});
</script>
@endsection
