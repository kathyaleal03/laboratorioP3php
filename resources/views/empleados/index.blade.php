@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Empleados</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('empleados.statistics') }}" class="btn btn-outline-secondary">
            <i class="bi bi-bar-chart-line me-1"></i> Ver estadísticas
        </a>
        <a href="{{ route('empleados.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Crear empleado
        </a>
    </div>
</div>

{{-- Filtros de búsqueda --}}
<form method="GET" class="mb-3">
   <div class="row g-3">

    <div class="col-sm">
        <label class="form-label small">Nombre</label>
        <input type="text" name="nombre" class="form-control form-control-sm" value="{{ request('nombre') }}" placeholder="Buscar por nombre">
    </div>

    <div class="col-sm">
        <label class="form-label small">Orden</label>
        <select name="orden" class="form-select form-select-sm">
            <option value="">— Por defecto —</option>
            <option value="newest" @if(request('orden') == 'nuevos') selected @endif>Más recientes</option>
            <option value="oldest" @if(request('orden') == 'recientes') selected @endif>Más antiguos</option>
        </select>
    </div>

</div>
    <div class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label small">Departamento</label>
            <select name="departamento" class="form-select form-select-sm">
                <option value="">— Todos —</option>
                @foreach(($departamentos ?? []) as $dep)
                    <option value="{{ $dep }}" @if(request('departamento') == $dep) selected @endif>{{ $dep }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <label class="form-label small">Puesto</label>
            <select name="puesto" class="form-select form-select-sm">
                <option value="">— Todos —</option>
                @foreach(($puestos ?? []) as $p)
                    <option value="{{ $p }}" @if(request('puesto') == $p) selected @endif>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <label class="form-label small">Salario min.</label>
            <input type="number" step="0.01" name="salario_min" class="form-control form-control-sm" value="{{ request('salario_min') }}" placeholder="0.00">
        </div>
        <div class="col-sm-2">
            <label class="form-label small">Salario max.</label>
            <input type="number" step="0.01" min=0  name="salario_max" class="form-control form-control-sm" value="{{ request('salario_max') }}" placeholder="0.00">
        </div>
        <div class="col-sm-2">
            <label class="form-label small">Estado</label>
            <select name="estado" class="form-select form-select-sm">
                <option value="">— Todos —</option>
                <option value="1" @if(request('estado') === '1') selected @endif>Activo</option>
                <option value="0" @if(request('estado') === '0') selected @endif>Inactivo</option>
            </select>
        </div>
        
        <div class="col-12 mt-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
            <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
        </div>
    </div>
</form>

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
   
    {{ $empleados->links('pagination::bootstrap-5') }}
</div>

@endsection
