@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar empleado</h1>

    <form action="{{ route('empleados.update', $empleado->id_empleado) }}" method="POST">
        @method('PUT')
        @include('empleados._form')
    </form>

    <a href="{{ route('empleados.index') }}" class="btn btn-link mt-3">Volver</a>
</div>
@endsection
