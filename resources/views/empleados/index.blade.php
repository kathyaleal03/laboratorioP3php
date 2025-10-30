@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Empleados</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('empleados.create') }}" class="btn btn-primary mb-3">Crear empleado</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Puesto</th>
                <th>Salario base</th>
                <th>Bonificación</th>
                <th>Descuento</th>
                <th>Salario neto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($empleados as $emp)
            <tr>
                <td>{{ $emp->nombre }}</td>
                <td>{{ $emp->departamento }}</td>
                <td>{{ $emp->puesto }}</td>
                <td>{{ $emp->salario_base }}</td>
                <td>{{ $emp->bonificacion }}</td>
                <td>{{ $emp->descuento }}</td>
                <td>{{ $emp->salario_neto }}</td>
                <td>{{ $emp->estado ? 'Activo' : 'Inactivo' }}</td>
                <td>
                    <a href="{{ route('empleados.show', $emp->id_empleado) }}" class="btn btn-sm btn-secondary">Ver</a>
                    <a href="{{ route('empleados.edit', $emp->id_empleado) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('empleados.destroy', $emp->id_empleado) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Seguro que quieres marcar como inactivo este empleado?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Desactivar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $empleados->links() }}
</div>
@endsection
