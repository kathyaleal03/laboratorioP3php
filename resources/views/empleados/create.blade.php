@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear empleado</h1>

    <form action="{{ route('empleados.store') }}" method="POST">
        @include('empleados._form')
    </form>

    <a href="{{ route('empleados.index') }}" class="btn btn-link mt-3">Volver</a>
</div>
@endsection
