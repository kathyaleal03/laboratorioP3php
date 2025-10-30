@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 900px;">
    <div class="card card-shadow">
        <div class="card-body">
            <h3 class="card-title mb-3">Crear empleado</h3>

            <form action="{{ route('empleados.store') }}" method="POST">
                @include('empleados._form')
            </form>
        </div>
    </div>
</div>

@endsection
