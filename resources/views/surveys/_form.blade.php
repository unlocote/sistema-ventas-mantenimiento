@csrf

<div class="mb-3">
    <label class="form-label">Nombre de la encuesta</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $encuesta->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', $encuesta->description ?? '') }}</textarea>
</div>

<hr>

<h4>Preguntas</h4>

<div id="preguntasContainer">
    @if (!empty($encuesta?->preguntas))
        @foreach ($encuesta->preguntas as $i => $pregunta)
            <div class="card mb-3 pregunta-item" data-index="{{ $i }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label>Pregunta {{ $i + 1 }}</label>
                        <button type="button" class="btn btn-danger btn-sm remove-pregunta">Eliminar pregunta</button>
                    </div>

                    <input type="hidden" name="preguntas[{{ $i }}][id]" value="{{ $pregunta->id }}">
                    <input type="text" name="preguntas[{{ $i }}][question]" class="form-control mb-3" placeholder="Texto de la pregunta" value="{{ $pregunta->question }}" required>

                    <div class="opcionesContainer">
                        <label>Opciones de respuesta:</label>

                        @foreach ($pregunta->opcionesRespuesta as $j => $opcion)
                            <div class="input-group mb-2 opcion-item">
                                <input type="hidden" name="preguntas[{{ $i }}][opciones][{{ $j }}][id]" value="{{ $opcion->id }}">
                                <input type="text" name="preguntas[{{ $i }}][opciones][{{ $j }}][answer]" class="form-control" placeholder="Texto de opción" value="{{ $opcion->answer }}" required>
                                <button type="button" class="btn btn-outline-danger remove-opcion">✖</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm add-opcion mt-2">➕ Añadir opción</button>
                </div>
            </div>
        @endforeach
    @endif
</div>

<button type="button" id="addPregunta" class="btn btn-outline-success">➕ Añadir pregunta</button>

<hr>

<div class="text-end mt-3">
    <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">{{ $btnText ?? 'Guardar' }}</button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const preguntasContainer = document.getElementById('preguntasContainer');
    const addPreguntaBtn = document.getElementById('addPregunta');

    // Añadir pregunta
    addPreguntaBtn.addEventListener('click', () => {
        const index = preguntasContainer.querySelectorAll('.pregunta-item').length;
        const preguntaHTML = `
            <div class="card mb-3 pregunta-item" data-index="${index}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label>Pregunta ${index + 1}</label>
                        <button type="button" class="btn btn-danger btn-sm remove-pregunta">Eliminar pregunta</button>
                    </div>

                    <input type="text" name="preguntas[${index}][question]" class="form-control mb-3" placeholder="Texto de la pregunta" required>

                    <div class="opcionesContainer">
                        <label>Opciones de respuesta:</label>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm add-opcion mt-2">➕ Añadir opción</button>
                </div>
            </div>
        `;
        preguntasContainer.insertAdjacentHTML('beforeend', preguntaHTML);
    });

    // Eliminar pregunta u opción (delegación)
    preguntasContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-pregunta')) {
            e.target.closest('.pregunta-item').remove();
        }

        if (e.target.classList.contains('remove-opcion')) {
            e.target.closest('.opcion-item').remove();
        }

        if (e.target.classList.contains('add-opcion')) {
            const preguntaCard = e.target.closest('.pregunta-item');
            const index = preguntaCard.dataset.index;
            const opcionesContainer = preguntaCard.querySelector('.opcionesContainer');
            const count = opcionesContainer.querySelectorAll('.opcion-item').length;

            const opcionHTML = `
                <div class="input-group mb-2 opcion-item">
                    <input type="text" name="preguntas[${index}][opciones][${count}][answer]" class="form-control" placeholder="Texto de opción" required>
                    <button type="button" class="btn btn-outline-danger remove-opcion">✖</button>
                </div>
            `;
            opcionesContainer.insertAdjacentHTML('beforeend', opcionHTML);
        }
    });
});
</script>
@endpush
