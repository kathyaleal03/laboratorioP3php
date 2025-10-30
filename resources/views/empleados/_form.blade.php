@csrf

<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $empleado->nombre ?? '') }}" required maxlength="100">
    @error('nombre')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="departamento" class="form-label">Departamento</label>
    <input type="text" name="departamento" id="departamento" class="form-control" value="{{ old('departamento', $empleado->departamento ?? '') }}" maxlength="50">
    @error('departamento')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="puesto" class="form-label">Puesto</label>
    <input type="text" name="puesto" id="puesto" class="form-control" value="{{ old('puesto', $empleado->puesto ?? '') }}" maxlength="50">
    @error('puesto')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="salario_base" class="form-label">Salario base</label>
    <input type="number" step="0.01" name="salario_base" id="salario_base" class="form-control" value="{{ old('salario_base', $empleado->salario_base ?? '') }}" required>
    @error('salario_base')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bonificacion" class="form-label">Bonificación</label>
    <input type="number" step="0.01" name="bonificacion" id="bonificacion" class="form-control" value="{{ old('bonificacion', $empleado->bonificacion ?? 0) }}">
    @error('bonificacion')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="descuento" class="form-label">Descuento</label>
    <input type="number" step="0.01" name="descuento" id="descuento" class="form-control" value="{{ old('descuento', $empleado->descuento ?? 0) }}">
    @error('descuento')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="fecha_contratacion" class="form-label">Fecha de contratación</label>
    <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="{{ old('fecha_contratacion', isset($empleado->fecha_contratacion) ? $empleado->fecha_contratacion->format('Y-m-d') : '') }}" required>
    @error('fecha_contratacion')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', isset($empleado->fecha_nacimiento) ? $empleado->fecha_nacimiento->format('Y-m-d') : '') }}">
    @error('fecha_nacimiento')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="sexo" class="form-label">Sexo</label>
    <select name="sexo" id="sexo" class="form-control">
        <option value="" {{ old('sexo', $empleado->sexo ?? '') == '' ? 'selected' : '' }}>--</option>
        <option value="M" {{ old('sexo', $empleado->sexo ?? '') == 'M' ? 'selected' : '' }}>M</option>
        <option value="F" {{ old('sexo', $empleado->sexo ?? '') == 'F' ? 'selected' : '' }}>F</option>
        <option value="O" {{ old('sexo', $empleado->sexo ?? '') == 'O' ? 'selected' : '' }}>O</option>
    </select>
    @error('sexo')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="evaluacion_desempeno" class="form-label">Evaluación (%)</label>
    <input type="number" step="0.01" name="evaluacion_desempeno" id="evaluacion_desempeno" class="form-control" value="{{ old('evaluacion_desempeno', $empleado->evaluacion_desempeno ?? 0) }}">
    @error('evaluacion_desempeno')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="estado" class="form-label">Estado</label>
    <select name="estado" id="estado" class="form-control">
        <option value="1" {{ old('estado', $empleado->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ old('estado', $empleado->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
    </select>
    @error('estado')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<button class="btn btn-primary">Guardar</button>
