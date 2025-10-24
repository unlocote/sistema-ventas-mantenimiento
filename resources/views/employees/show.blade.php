@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Empleado</h1>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{ $employee->name }}</h4>

            <p><strong>ID:</strong> {{ $employee->id }}</p>
            <p><strong>Tipo de identificación:</strong> {{ $employee->tipoId->shortName ?? '—' }}</p>
            <p><strong>Número de identificación:</strong> {{ $employee->identification }}</p>
            <p><strong>Teléfono:</strong> {{ $employee->phoneNumber }}</p>
            <p><strong>Dirección:</strong> {{ $employee->address }}</p>
            <p><strong>Correo electrónico:</strong> {{ $employee->email }}</p>
            <hr>

            <h5>Contratos</h5>
            <ul class="list-group">
                @foreach ($employee->contratos as $contrato)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cargo:</strong> {{ $contrato->cargo->name ?? 'N/A' }}<br>
                            <strong>Fecha Creación:</strong> {{ $contrato->creation_date }}<br>
                            <strong>Fecha Inicio:</strong> {{ $contrato->start_date }}<br>
                            <strong>Fecha Fin:</strong> {{ $contrato->end_date  ?? 'Actual' }}<br>
                        </div>

                        @if ($contrato->document)
                            <a href="{{ route('contracts.download', $contrato->id) }}" class="btn btn-sm btn-primary">
                                Descargar Documento
                            </a>
                        @else
                            <span class="text-muted">Sin documento</span>
                        @endif
                    </li>
                @endforeach
            </ul>

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Volver</a>

        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('¿Seguro que deseas eliminar este employee?')">
                Eliminar
            </button>
        </form>
    </div>
</div>
@endsection