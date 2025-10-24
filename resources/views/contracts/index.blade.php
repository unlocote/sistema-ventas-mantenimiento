<div class="card mt-4">
    <div class="card-header">
        <h5>Contratos del Empleado</h5>
    </div>
    <div class="card-body">
        @if(isset($employee) && $employee->contratos->count() > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha de inicio</th>
                        <th>Fecha de fin</th>
                        <th>Cargo</th>
                        <th>Documento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empleado->contratos as $contrato)
                        <tr>
                            <td>{{ $contrato->id }}</td>
                            <td>{{ $contrato->fecha_inicio }}</td>
                            <td>{{ $contrato->fecha_fin }}</td>
                            <td>{{ $contrato->cargo->name ?? 'Sin cargo' }}</td>
                            <td>
                                @if($contrato->document)
                                    <a href="{{ route('contratos.download', $contrato->id) }}" class="btn btn-sm btn-primary">
                                        Descargar
                                    </a>
                                @else
                                    <span class="text-muted">Sin documento</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No hay contratos registrados.</p>
        @endif

        {{-- Botón para agregar un nuevo contrato --}}
        <div class="mt-3">
            <a href="{{ route('contratos.create') }}" class="btn btn-success btn-sm">Agregar Contrato</a>
        </div>
    </div>
</div>