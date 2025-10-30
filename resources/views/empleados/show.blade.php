@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Empleado: {{ $empleado->nombre }}</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>Departamento:</strong> {{ $empleado->departamento }}</p>
            <p><strong>Puesto:</strong> {{ $empleado->puesto }}</p>
            <p><strong>Salario base:</strong> {{ $empleado->salario_base }}</p>
            <p><strong>Bonificación:</strong> {{ $empleado->bonificacion }}</p>
            <p><strong>Descuento:</strong> {{ $empleado->descuento }}</p>
            <p><strong>Salario neto:</strong> {{ $empleado->salario_neto }}</p>
            <p><strong>Fecha contratación:</strong> {{ optional($empleado->fecha_contratacion)->format('Y-m-d') }}</p>
            <p><strong>Fecha nacimiento:</strong> {{ optional($empleado->fecha_nacimiento)->format('Y-m-d') }}</p>
            <p><strong>Sexo:</strong> {{ $empleado->sexo }}</p>
            <p><strong>Evaluación:</strong> {{ $empleado->evaluacion_desempeno }}</p>
            <p><strong>Estado:</strong> {{ $empleado->estado ? 'Activo' : 'Inactivo' }}</p>
        </div>
    </div>

    <a href="{{ route('empleados.index') }}" class="btn btn-link mt-3">Volver</a>
</div>
@endsection
