@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Empleados</h2>
    <a href="{{ route('empleados.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Crear empleado
    </a>
</div>

<div class="card card-shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Departamento</th>
                        <th>Puesto</th>
                        <th class="text-end">Salario base</th>
                        <th class="text-end">Bonif.</th>
                        <th class="text-end">Descuento</th>
                        <th class="text-end">Salario neto</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($empleados as $emp)
                    <tr>
                        <td class="fw-semibold">{{ $emp->nombre }}</td>
                        <td>{{ $emp->departamento ?? '-' }}</td>
                        <td>{{ $emp->puesto ?? '-' }}</td>
                        <td class="text-end">${{ number_format($emp->salario_base, 2) }}</td>
                        <td class="text-end">${{ number_format($emp->bonificacion ?? 0, 2) }}</td>
                        <td class="text-end">${{ number_format($emp->descuento ?? 0, 2) }}</td>
                        <td class="text-end">${{ number_format($emp->salario_neto, 2) }}</td>
                        <td>
                            @if($emp->estado)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('empleados.show', $emp->id_empleado) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('empleados.edit', $emp->id_empleado) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('empleados.destroy', $emp->id_empleado) }}" method="POST" style="display:inline-block" onsubmit="return confirm('¿Marcar como inactivo este empleado?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Desactivar">
                                    <i class="bi bi-person-x"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">No hay empleados aún. Crea uno nuevo.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{-- Forzar vista de paginación de Bootstrap 5 para evitar conflictos con estilos Tailwind u otros --}}
    {{ $empleados->links('pagination::bootstrap-5') }}
</div>

@endsection
