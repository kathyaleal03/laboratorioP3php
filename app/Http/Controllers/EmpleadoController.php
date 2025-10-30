<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /** Mostrar lista paginada */
    public function index()
    {
        $empleados = Empleado::orderBy('nombre')->paginate(10);
        return view('empleados.index', compact('empleados'));
    }

    /**
     * Mostrar estadísticas financieras y demográficas.
     */
    public function statistics()
    {
        // Financieros — sólo empleados activos (estado = 1)
        $avgSalaryOverall = (float) round(Empleado::where('estado', 1)->avg('salario_base') ?? 0, 2);
        $avgSalaryPerDept = Empleado::where('estado', 1)
            ->selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(salario_base) as avg_salary')
            ->groupBy('departamento')
            ->get();

        $totalBonuses = (float) Empleado::where('estado', 1)->sum('bonificacion');
        $totalDiscounts = (float) Empleado::where('estado', 1)->sum('descuento');

        // Crecimiento salario neto vs año anterior: comparar promedio neto de contrataciones por año
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $avgNetForYear = function ($year) {
            // considerar sólo empleados activos
            $emps = Empleado::whereYear('fecha_contratacion', $year)->where('estado', 1)->get();
            if ($emps->isEmpty()) return 0.0;
            $avg = $emps->map(function ($e) {
                return ((float) $e->salario_base + (float) ($e->bonificacion ?? 0) - (float) ($e->descuento ?? 0));
            })->average();
            return (float) $avg;
        };

        $avgNetCurrentYear = $avgNetForYear($currentYear);
        $avgNetLastYear = $avgNetForYear($lastYear);

        $growthPct = 0.0;
        if ($avgNetLastYear > 0) {
            $growthPct = round((($avgNetCurrentYear - $avgNetLastYear) / $avgNetLastYear) * 100, 2);
        }

        // Demográficos
        $ages = Empleado::whereNotNull('fecha_nacimiento')->get()->map(function ($e) {
            try {
                return \Carbon\Carbon::parse($e->fecha_nacimiento)->age;
            } catch (\Exception $ex) {
                return null;
            }
        })->filter();

        $avgAge = $ages->count() ? round($ages->average(), 1) : 0.0;

        $genderCounts = Empleado::selectRaw('COALESCE(sexo, "O") as sexo, COUNT(*) as cnt')
            ->groupBy('sexo')
            ->pluck('cnt', 'sexo')
            ->toArray();

        $totalPersons = array_sum($genderCounts) ?: Empleado::count() ?: 1;
        $genderDistribution = [
            'M' => round((($genderCounts['M'] ?? 0) / $totalPersons) * 100, 1),
            'F' => round((($genderCounts['F'] ?? 0) / $totalPersons) * 100, 1),
            'O' => round((($genderCounts['O'] ?? 0) / $totalPersons) * 100, 1),
        ];

        // Edad promedio por puesto directivo vs operativa
        // Clasificamos 'directivo' por presencia de palabras clave en el campo puesto
        $leadKeywords = ['Director','Gerente','Jefe','Chief','Manager','Head'];

        $directivos = Empleado::whereNotNull('puesto')
            ->get()
            ->filter(function ($e) use ($leadKeywords) {
                foreach ($leadKeywords as $kw) {
                    if (stripos($e->puesto, $kw) !== false) return true;
                }
                return false;
            });

        $operativos = Empleado::where(function($q) use ($leadKeywords) {
                // include those without lead keywords
                foreach ($leadKeywords as $kw) {
                    $q->whereRaw('COALESCE(puesto, "") NOT LIKE ?', ["%{$kw}%"]);
                }
            })->get();

        $avgAgeDirectivos = $directivos->pluck('fecha_nacimiento')->filter()->map(function($d){ try { return \Carbon\Carbon::parse($d)->age; } catch(\Exception $ex){ return null; } })->filter()->avg() ?: 0.0;
        $avgAgeOperativos = $operativos->pluck('fecha_nacimiento')->filter()->map(function($d){ try { return \Carbon\Carbon::parse($d)->age; } catch(\Exception $ex){ return null; } })->filter()->avg() ?: 0.0;

        // ------------------
        // Desempeño
        // ------------------
        $avgEvalPerDept = Empleado::selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(evaluacion_desempeno) as avg_eval')
            ->groupBy('departamento')
            ->get();

        $evalRows = Empleado::whereNotNull('evaluacion_desempeno')->get();
        $employeesWithEvalGT95 = Empleado::where('evaluacion_desempeno', '>', 95)->count();
        $totalEvalCount = $evalRows->count();
        $percentEvalGT70 = $totalEvalCount ? round(($evalRows->where('evaluacion_desempeno', '>', 70)->count() / $totalEvalCount) * 100, 1) : 0.0;
        // Correlación salario-desempeño: sólo activos
        $salaryEvalRows = Empleado::where('estado', 1)->whereNotNull('evaluacion_desempeno')->whereNotNull('salario_base')->get();
        $salaryEvalCorr = 0.0;
        if ($salaryEvalRows->count() >= 2) {
            $xs = $salaryEvalRows->pluck('salario_base')->map(fn($v) => (float) $v)->values()->all();
            $ys = $salaryEvalRows->pluck('evaluacion_desempeno')->map(fn($v) => (float) $v)->values()->all();
            $n = count($xs);
            $meanX = array_sum($xs) / $n;
            $meanY = array_sum($ys) / $n;
            $cov = 0.0; $varX = 0.0; $varY = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $dx = $xs[$i] - $meanX;
                $dy = $ys[$i] - $meanY;
                $cov += $dx * $dy;
                $varX += $dx * $dx;
                $varY += $dy * $dy;
            }
            if ($varX > 0 && $varY > 0) {
                $salaryEvalCorr = round($cov / sqrt($varX * $varY), 2);
            }
        }

        // Evaluación promedio global (por departamento mostramos arriba)
        $globalAvgEvaluation = $evalRows->count() ? round($evalRows->avg('evaluacion_desempeno'), 2) : 0.0;

        // ------------------
        // Antigüedad / Permanencia (sólo empleados activos)
        $now = \Carbon\Carbon::now();
        $tenureRows = Empleado::where('estado', 1)->whereNotNull('fecha_contratacion')->whereNotNull('salario_base')->get();
        $tenures = $tenureRows->map(function($e) use ($now) {
            try {
                $d = \Carbon\Carbon::parse($e->fecha_contratacion);
                // años con decimales
                return $d->diffInDays($now) / 365.25;
            } catch (\Exception $ex) {
                return null;
            }
        })->filter()->values();

        $avgTenure = $tenures->count() ? round($tenures->avg(), 2) : 0.0; // antigüedad promedio
        // tiempo promedio de permanencia -> mediana de tenures
        $medianTenure = 0.0;
        if ($tenures->count()) {
            $sorted = $tenures->sort()->values()->all();
            $m = count($sorted);
            $mid = (int) floor(($m - 1) / 2);
            if ($m % 2) {
                $medianTenure = round($sorted[$mid], 2);
            } else {
                $medianTenure = round(($sorted[$mid] + $sorted[$mid + 1]) / 2, 2);
            }
        }

        // Correlación antigüedad-salario
        $tenureSalaryCorr = 0.0;
        if ($tenures->count() >= 2) {
            $xs = $tenureRows->map(function($e) use ($now) { try { return (\Carbon\Carbon::parse($e->fecha_contratacion)->diffInDays($now) / 365.25); } catch (\Exception $ex) { return null; } })->filter()->values()->all();
            $ys = $tenureRows->map(fn($e) => (float) $e->salario_base)->values()->all();
            if (count($xs) === count($ys) && count($xs) >= 2) {
                $n = count($xs);
                $meanX = array_sum($xs) / $n;
                $meanY = array_sum($ys) / $n;
                $cov = 0.0; $varX = 0.0; $varY = 0.0;
                for ($i = 0; $i < $n; $i++) {
                    $dx = $xs[$i] - $meanX;
                    $dy = $ys[$i] - $meanY;
                    $cov += $dx * $dy;
                    $varX += $dx * $dx;
                    $varY += $dy * $dy;
                }
                if ($varX > 0 && $varY > 0) {
                    $tenureSalaryCorr = round($cov / sqrt($varX * $varY), 2);
                }
            }
        }

        $countTenureOver10 = $tenures->where('>', 10)->count();
        $percentOver10 = $tenures->count() ? round(($countTenureOver10 / $tenures->count()) * 100, 1) : 0.0;

        // Preparar datos para gráficos
        $chartDeptLabels = $avgSalaryPerDept->pluck('departamento')->map(fn($v) => (string) $v)->all();
        $chartDeptData = $avgSalaryPerDept->pluck('avg_salary')->map(fn($v) => (float) $v)->all();
        // Scatter: sólo empleados activos
        $scatterRows = Empleado::where('estado', 1)->whereNotNull('salario_base')->whereNotNull('evaluacion_desempeno')->get();
        $chartScatterData = $scatterRows->map(function($e){
            return ['x' => (float) $e->salario_base, 'y' => (float) $e->evaluacion_desempeno];
        })->values()->all();

        $chartGenderLabels = ['M', 'F', 'O'];
        $chartGenderData = [($genderCounts['M'] ?? 0), ($genderCounts['F'] ?? 0), ($genderCounts['O'] ?? 0)];

        // Evolución de salario promedio por año (por fecha_contratacion) — sólo activos
        $salaryByYear = Empleado::where('estado', 1)->whereNotNull('fecha_contratacion')
            ->selectRaw('YEAR(fecha_contratacion) as year, AVG(salario_base) as avg_salary')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        $chartYears = $salaryByYear->pluck('year')->map(fn($y) => (string) $y)->all();
        $chartYearsData = $salaryByYear->pluck('avg_salary')->map(fn($v) => (float) $v)->all();

        return view('empleados.statistics', compact(
            'avgSalaryOverall', 'avgSalaryPerDept', 'totalBonuses', 'totalDiscounts', 'growthPct',
            'avgAge', 'genderDistribution', 'avgAgeDirectivos', 'avgAgeOperativos',
            'avgEvalPerDept', 'employeesWithEvalGT95', 'percentEvalGT70', 'salaryEvalCorr', 'globalAvgEvaluation',
            'avgTenure', 'medianTenure', 'tenureSalaryCorr', 'percentOver10',
            // charts
            'chartDeptLabels', 'chartDeptData', 'chartScatterData', 'chartGenderLabels', 'chartGenderData', 'chartYears', 'chartYearsData'
        ));
    }

    /**
     * Helper para generar datasets para gráficos y anexarlos a la vista de estadísticas.
     * (Se podría refactorizar a servicios, pero lo incluimos aquí por simplicidad.)
     */
    protected function appendChartData(array $data = [])
    {
        // Este método no se usa directamente; mantenido para posible refactor.
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
        if (!empty($data['fecha_nacimiento']) && !empty($data['sexo'])) {

                  
            $fechaNacimiento = Carbon::parse($data['fecha_nacimiento']);
            $fechaContratacion = Carbon::parse($data['fecha_contratacion']);

                    
                $años_empleado = $fechaNacimiento->diffInYears($fechaContratacion);

                    if ($años_empleado >= 65 && $data['sexo'] == 'M') {
                        return redirect()->back()
                        ->withInput()
                        ->withErrors(['fecha_nacimiento' => 'No se puede actualizar un empleado sus años no son aptos para el trabajo.']);
                    } 
                    else if ($años_empleado >= 60 && $data['sexo'] == 'F') {
                        return redirect()->back()
                        ->withInput()
                        ->withErrors(['fecha_nacimiento' => 'No se puede actualizar un empleado sus años no son aptos para el trabajo.']);
                    }
        }

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

        
         if (!empty($data['fecha_nacimiento']) && !empty($data['sexo'])) {
            $fechaNacimiento = Carbon::parse($data['fecha_nacimiento']);
            $fechaContratacion = Carbon::parse($data['fecha_contratacion']);
                    
            $años_empleado = $fechaNacimiento->diffInYears($fechaContratacion);

            if ($años_empleado >= 65 && $data['sexo'] == 'M') {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fecha_nacimiento' => 'No se puede actualizar un empleado sus años no son aptos para el trabajo.']);
            } 
            else if ($años_empleado >= 60 && $data['sexo'] == 'F') {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fecha_nacimiento' => 'No se puede actualizar una empleada sus años no son aptos para el trabajo.']);
            }
        }
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
