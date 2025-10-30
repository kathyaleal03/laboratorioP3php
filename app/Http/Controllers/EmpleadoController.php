<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /** Mostrar lista paginada */
    public function index()
    {
        $empleados = Empleado::orderBy('nombre')->paginate(10);
        return view('empleados.index', compact('empleados'));
    }

    /** Mostrar formulario de creación */
    public function create()
    {
        return view('empleados.create');
    }

    /** Almacenar nuevo empleado */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'departamento' => 'nullable|string|max:50',
            'puesto' => 'nullable|string|max:50',
            'salario_base' => 'required|numeric|min:0',
            'bonificacion' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'fecha_contratacion' => 'required|date',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F,O',
            'evaluacion_desempeno' => 'nullable|numeric|min:0|max:100',
            'estado' => 'nullable|in:0,1',
        ]);

        // Asegurar valores por defecto si no vienen
        $data['bonificacion'] = $data['bonificacion'] ?? 0.00;
        $data['descuento'] = $data['descuento'] ?? 0.00;
        $data['evaluacion_desempeno'] = $data['evaluacion_desempeno'] ?? 0.00;
        $data['estado'] = $data['estado'] ?? 1;

        Empleado::create($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    /** Mostrar empleado */
    public function show($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('empleados.show', compact('empleado'));
    }

    /** Mostrar formulario de edición */
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('empleados.edit', compact('empleado'));
    }

    /** Actualizar empleado */
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'departamento' => 'nullable|string|max:50',
            'puesto' => 'nullable|string|max:50',
            'salario_base' => 'required|numeric|min:0',
            'bonificacion' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'fecha_contratacion' => 'required|date',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F,O',
            'evaluacion_desempeno' => 'nullable|numeric|min:0|max:100',
            'estado' => 'nullable|in:0,1',
        ]);

        $data['bonificacion'] = $data['bonificacion'] ?? 0.00;
        $data['descuento'] = $data['descuento'] ?? 0.00;
        $data['evaluacion_desempeno'] = $data['evaluacion_desempeno'] ?? 0.00;
        $data['estado'] = $data['estado'] ?? 1;

        $empleado->update($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    /** "Eliminar" empleado — marca como inactivo (estado = 0) */
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->estado = 0;
        $empleado->save();

        return redirect()->route('empleados.index')->with('success', 'Empleado marcado como inactivo.');
    }
}
