@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Empleado: {{ $empleado->nombre }}</h3>
        <div>
            <a href="{{ route('empleados.edit', $empleado->id_empleado) }}" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil"></i> Editar</a>
            <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
        </div>
    </div>

    <div class="card card-shadow">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">Departamento</dt>
                <dd class="col-sm-8">{{ $empleado->departamento ?? '-' }}</dd>

                <dt class="col-sm-4">Puesto</dt>
                <dd class="col-sm-8">{{ $empleado->puesto ?? '-' }}</dd>

                <dt class="col-sm-4">Salario base</dt>
                <dd class="col-sm-8">${{ number_format($empleado->salario_base, 2) }}</dd>

                <dt class="col-sm-4">Bonificación</dt>
                <dd class="col-sm-8">${{ number_format($empleado->bonificacion ?? 0, 2) }}</dd>

                <dt class="col-sm-4">Descuento</dt>
                <dd class="col-sm-8">${{ number_format($empleado->descuento ?? 0, 2) }}</dd>

                <dt class="col-sm-4">Salario neto</dt>
                <dd class="col-sm-8">${{ number_format($empleado->salario_neto, 2) }}</dd>

                <dt class="col-sm-4">Salario bruto</dt>
                <dd class="col-sm-8">${{ number_format($empleado->salario_bruto ?? 0, 2) }}</dd>

                <dt class="col-sm-4">Edad</dt>
                <dd class="col-sm-8">{{ $empleado->edad !== null ? $empleado->edad . ' años' : '-' }}</dd>

                <dt class="col-sm-4">Antigüedad</dt>
                <dd class="col-sm-8">{{ $empleado->antiguedad !== null ? $empleado->antiguedad . ' años' : '-' }}</dd>

                <dt class="col-sm-4">Relación Desempeño/Salario</dt>
                <dd class="col-sm-8">@if($empleado->relacion_desempeno_salario !== null) {{ number_format($empleado->relacion_desempeno_salario, 4) }} @else - @endif</dd>

                <dt class="col-sm-4">Fecha contratación</dt>
                <dd class="col-sm-8">{{ optional($empleado->fecha_contratacion)->format('Y-m-d') }}</dd>

                <dt class="col-sm-4">Fecha nacimiento</dt>
                <dd class="col-sm-8">{{ optional($empleado->fecha_nacimiento)->format('Y-m-d') ?? '-' }}</dd>

                <dt class="col-sm-4">Sexo</dt>
                <dd class="col-sm-8">{{ $empleado->sexo ?? '-' }}</dd>

                <dt class="col-sm-4">Evaluación</dt>
                <dd class="col-sm-8">{{ number_format($empleado->evaluacion_desempeno ?? 0, 2) }}%</dd>

                <dt class="col-sm-4">Estado</dt>
                <dd class="col-sm-8">
                    @if($empleado->estado)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>

@endsection
