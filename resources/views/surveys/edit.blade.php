@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar encuesta</h1>

    <form id="formEncuesta" action="{{ route('surveys.update', $encuesta->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre de la encuesta</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ $encuesta->name }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ $encuesta->description }}</textarea>
        </div>

        <hr>
        <h4>Preguntas</h4>

        <div id="preguntasContainer" class="mt-3"></div>

        <button type="button" class="btn btn-outline-success mt-2" id="btnAddPregunta">
            ➕ Añadir pregunta
        </button>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">Actualizar encuesta</button>
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Modal: Crear/Editar pregunta -->
<div class="modal fade" id="modalPregunta" tabindex="-1" aria-labelledby="modalPreguntaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPreguntaLabel">Añadir pregunta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="preguntaEditIndex">

        <div class="mb-3">
          <label class="form-label">Texto de la pregunta</label>
          <input type="text" id="preguntaTexto" class="form-control" placeholder="Ej: ¿Cuál es tu color favorito?">
        </div>

        <h6>Opciones de respuesta</h6>
        <div id="opcionesContainer"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnAddOpcion">+ Añadir opción</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarPregunta">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const preguntasContainer = document.getElementById('preguntasContainer');
    const btnAddPregunta = document.getElementById('btnAddPregunta');
    const modal = new bootstrap.Modal(document.getElementById('modalPregunta'));
    const preguntaTexto = document.getElementById('preguntaTexto');
    const opcionesContainer = document.getElementById('opcionesContainer');
    const btnAddOpcion = document.getElementById('btnAddOpcion');
    const btnGuardarPregunta = document.getElementById('btnGuardarPregunta');
    const preguntaEditIndex = document.getElementById('preguntaEditIndex');
    const form = document.getElementById('formEncuesta');

    // Cargar preguntas existentes desde PHP
    let preguntas = @json($encuesta->preguntas->map(function($p) {
        return [
            'question' => $p->question,
            'opciones' => $p->opcionesRespuesta->pluck('answer')->toArray(),
        ];
    }));

    // === AÑADIR PREGUNTA ===
    btnAddPregunta.addEventListener('click', () => abrirModalNuevaPregunta());

    function abrirModalNuevaPregunta() {
        preguntaEditIndex.value = '';
        preguntaTexto.value = '';
        opcionesContainer.innerHTML = '';
        agregarOpcion('');
        agregarOpcion('');
        agregarOpcion('');
        document.getElementById('modalPreguntaLabel').textContent = 'Añadir pregunta';
        modal.show();
    }

    // === OPCIONES ===
    btnAddOpcion.addEventListener('click', () => agregarOpcion(''));

    function agregarOpcion(valor) {
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" class="form-control opcionInput" placeholder="Texto de opción" value="${valor}">
            <button type="button" class="btn btn-danger btnEliminarOpcion">✕</button>
        `;
        div.querySelector('.btnEliminarOpcion').addEventListener('click', () => div.remove());
        opcionesContainer.appendChild(div);
    }

    // === GUARDAR PREGUNTA ===
    btnGuardarPregunta.addEventListener('click', () => {
        const texto = preguntaTexto.value.trim();
        const opciones = Array.from(opcionesContainer.querySelectorAll('.opcionInput'))
                              .map(i => i.value.trim())
                              .filter(v => v !== '');

        if (!texto) {
            alert('Debe escribir el texto de la pregunta.');
            return;
        }
        if (opciones.length < 3) {
            alert('Debe ingresar al menos tres opciones.');
            return;
        }

        const index = preguntaEditIndex.value;
        if (index === '') {
            preguntas.push({ question: texto, opciones });
        } else {
            preguntas[index] = { question: texto, opciones };
        }

        modal.hide();
        renderPreguntas();
    });

    // === MOSTRAR LISTA DE PREGUNTAS ===
    function renderPreguntas() {
        preguntasContainer.innerHTML = '';
        if (preguntas.length === 0) {
            preguntasContainer.innerHTML = `<div class="alert alert-secondary text-center">No hay preguntas aún.</div>`;
            return;
        }

        preguntas.forEach((p, i) => {
            const card = document.createElement('div');
            card.classList.add('card', 'mt-2');
            card.innerHTML = `
                <div class="card-body">
                    <h5>${p.question}</h5>
                    <ul>${p.opciones.map(op => `<li>${op}</li>`).join('')}</ul>
                    <button type="button" class="btn btn-sm btn-primary me-2 btnEditar" data-index="${i}">✏ Editar</button>
                    <button type="button" class="btn btn-sm btn-danger btnEliminar" data-index="${i}">🗑 Eliminar</button>
                </div>
            `;
            preguntasContainer.appendChild(card);
        });
        attachEventosAcciones();
        updateHiddenInputs();
    }

    // === EDITAR / ELIMINAR ===
    function attachEventosAcciones() {
        document.querySelectorAll('.btnEditar').forEach(btn => {
            btn.addEventListener('click', e => {
                const index = e.target.dataset.index;
                abrirModalEditar(index);
            });
        });
        document.querySelectorAll('.btnEliminar').forEach(btn => {
            btn.addEventListener('click', e => {
                const index = e.target.dataset.index;
                if (confirm('¿Eliminar esta pregunta?')) {
                    preguntas.splice(index, 1);
                    renderPreguntas();
                }
            });
        });
    }

    function abrirModalEditar(index) {
        const p = preguntas[index];
        preguntaEditIndex.value = index;
        preguntaTexto.value = p.question;
        opcionesContainer.innerHTML = '';
        p.opciones.forEach(op => agregarOpcion(op));
        document.getElementById('modalPreguntaLabel').textContent = 'Editar pregunta';
        modal.show();
    }

    // === INPUTS OCULTOS ===
    function updateHiddenInputs() {
        document.querySelectorAll('.hiddenPregunta').forEach(e => e.remove());
        preguntas.forEach((p, i) => {
            const qInput = document.createElement('input');
            qInput.type = 'hidden';
            qInput.name = `preguntas[${i}][question]`;
            qInput.value = p.question;
            qInput.classList.add('hiddenPregunta');
            preguntasContainer.appendChild(qInput);

            p.opciones.forEach((op, j) => {
                const oInput = document.createElement('input');
                oInput.type = 'hidden';
                oInput.name = `preguntas[${i}][opciones][${j}]`;
                oInput.value = op;
                oInput.classList.add('hiddenPregunta');
                preguntasContainer.appendChild(oInput);
            });
        });
    }

    // === VALIDACIÓN FINAL ===
    form.addEventListener('submit', (e) => {
        const nombre = document.getElementById('name').value.trim();

        if (!nombre) {
            alert('El nombre de la encuesta es obligatorio.');
            e.preventDefault();
            return;
        }

        if (preguntas.length < 2) {
            alert('Debe agregar al menos dos preguntas antes de guardar.');
            e.preventDefault();
            return;
        }

        for (let i = 0; i < preguntas.length; i++) {
            const p = preguntas[i];
            if (!p.question.trim()) {
                alert(`La pregunta ${i + 1} no puede estar vacía.`);
                e.preventDefault();
                return;
            }
            if (p.opciones.length < 3) {
                alert(`La pregunta ${i + 1} debe tener al menos tres opciones.`);
                e.preventDefault();
                return;
            }
            for (let j = 0; j < p.opciones.length; j++) {
                if (!p.opciones[j].trim()) {
                    alert(`La opción ${j + 1} de la pregunta ${i + 1} no puede estar vacía.`);
                    e.preventDefault();
                    return;
                }
            }
        }
    });

    renderPreguntas(); // 👈 render inicial con los datos cargados
});
</script>
@endsection
