@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Empleados</h1>

    <a href="{{ route('employees.create') }}" class="btn btn-primary mb-3">Nuevo Empleado</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo Id</th>
                <th>Identificación</th>
                <th>Email</th>
                <th>Número de contratos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
            <tr>
                <td>{{ $employee->id }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->tipoId->shortName ?? '—' }}</td>
                <td>{{ $employee->identification }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->contratos_count }}</td>
                <td>
                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4">No hay proveedores registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection