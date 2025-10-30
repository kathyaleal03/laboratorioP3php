<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
   
    public function index()
    {
        
        $query = Empleado::query();

        if (request()->filled('nombre')) {
            $query->where('nombre', 'like', '%' . request('nombre') . '%');
        }
        if (request()->filled('departamento')) {
            $query->where('departamento', request('departamento'));
        }
        if (request()->filled('puesto')) {
            $query->where('puesto', request('puesto'));
        }
        if (request()->filled('salario_min')) {
            $query->where('salario_base', '>=', request('salario_min'));
        }
        if (request()->filled('salario_max')) {
            $query->where('salario_base', '<=', request('salario_max'));
        }
        if (request()->filled('estado')) {
            $query->where('estado', request('estado'));
        }

       
        $orden = request('orden');
        // Aceptar varios sinónimos en español/inglés desde la vista
        if (in_array($orden, ['nuevos', 'newest'], true)) {
            // Mostrar primero los más nuevos (fecha_contratacion descendente)
            $query->orderBy('fecha_contratacion', 'desc');
        } elseif (in_array($orden, ['recientes', 'oldest'], true)) {
            // Mostrar primero los más antiguos (fecha_contratacion ascendente)
            $query->orderBy('fecha_contratacion', 'asc');
        } else {
            $query->orderBy('nombre');
        }

        $empleados = $query->paginate(10)->appends(request()->except('page'));

        $departamentos = Empleado::whereNotNull('departamento')
            ->pluck('departamento')
            ->unique()
            ->sort()
            ->values()
            ->all();

        $puestos = Empleado::whereNotNull('puesto')
            ->pluck('puesto')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('empleados.index', compact('empleados', 'departamentos', 'puestos'));
    }

    /**
     * Mostrar estadísticas financieras y demográficas.
     */
    public function statistics()
    {
        // Financieros 
        $avgSalaryOverall = (float) round(Empleado::where('estado', 1)->avg('salario_base') ?? 0, 2);
        $avgSalaryPerDept = Empleado::where('estado', 1)
            ->selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(salario_base) as avg_salary')
            ->groupBy('departamento')
            ->get();

        $totalBonuses = (float) Empleado::where('estado', 1)->sum('bonificacion');
        $totalDiscounts = (float) Empleado::where('estado', 1)->sum('descuento');

      
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $avgNetForYear = function ($year) {

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

       
        $globalAvgEvaluation = $evalRows->count() ? round($evalRows->avg('evaluacion_desempeno'), 2) : 0.0;

        $now = \Carbon\Carbon::now();
        $tenureRows = Empleado::where('estado', 1)->whereNotNull('fecha_contratacion')->whereNotNull('salario_base')->get();
        $tenures = $tenureRows->map(function($e) use ($now) {
            try {
                $d = \Carbon\Carbon::parse($e->fecha_contratacion);
        
                return $d->diffInDays($now) / 365.25;
            } catch (\Exception $ex) {
                return null;
            }
        })->filter()->values();

        $avgTenure = $tenures->count() ? round($tenures->avg(), 2) : 0.0; // antigüedad promedio

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


        $chartDeptLabels = $avgSalaryPerDept->pluck('departamento')->map(fn($v) => (string) $v)->all();
        $chartDeptData = $avgSalaryPerDept->pluck('avg_salary')->map(fn($v) => (float) $v)->all();
 
        $scatterRows = Empleado::where('estado', 1)->whereNotNull('salario_base')->whereNotNull('evaluacion_desempeno')->get();
        $chartScatterData = $scatterRows->map(function($e){
            return ['x' => (float) $e->salario_base, 'y' => (float) $e->evaluacion_desempeno];
        })->values()->all();

        $chartGenderLabels = ['M', 'F', 'O'];
        $chartGenderData = [($genderCounts['M'] ?? 0), ($genderCounts['F'] ?? 0), ($genderCounts['O'] ?? 0)];

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
            'chartDeptLabels', 'chartDeptData', 'chartScatterData', 'chartGenderLabels', 'chartGenderData', 'chartYears', 'chartYearsData'
        ));
    }


    /**
     * Exportar estadísticas en CSV (descarga).
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        if ($format !== 'csv') {
            abort(400, 'Formato no soportado. Por ahora solo csv.');
        }

        // Recalcular métricas clave (misma lógica que en statistics)
        $avgSalaryOverall = (float) round(Empleado::where('estado', 1)->avg('salario_base') ?? 0, 2);
        $avgSalaryPerDept = Empleado::where('estado', 1)
            ->selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(salario_base) as avg_salary')
            ->groupBy('departamento')
            ->get();

        $totalBonuses = (float) Empleado::where('estado', 1)->sum('bonificacion');
        $totalDiscounts = (float) Empleado::where('estado', 1)->sum('descuento');

        $evalRows = Empleado::whereNotNull('evaluacion_desempeno')->get();
        $employeesWithEvalGT95 = Empleado::where('evaluacion_desempeno', '>', 95)->count();
        $totalEvalCount = $evalRows->count();
        $percentEvalGT70 = $totalEvalCount ? round(($evalRows->where('evaluacion_desempeno', '>', 70)->count() / $totalEvalCount) * 100, 1) : 0.0;

        $globalAvgEvaluation = $evalRows->count() ? round($evalRows->avg('evaluacion_desempeno'), 2) : 0.0;

        $ages = Empleado::whereNotNull('fecha_nacimiento')->get()->map(function ($e) {
            try {
                return \Carbon\Carbon::parse($e->fecha_nacimiento)->age;
            } catch (\Exception $ex) {
                return null;
            }
        })->filter();
        $avgAge = $ages->count() ? round($ages->average(), 1) : 0.0;

        $avgEvalPerDept = Empleado::selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(evaluacion_desempeno) as avg_eval')
            ->groupBy('departamento')
            ->get();

        $salaryByYear = Empleado::where('estado', 1)->whereNotNull('fecha_contratacion')
            ->selectRaw('YEAR(fecha_contratacion) as year, AVG(salario_base) as avg_salary')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $filename = 'report_estadisticas_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use (
            $avgSalaryOverall, $avgSalaryPerDept, $totalBonuses, $totalDiscounts,
            $globalAvgEvaluation, $employeesWithEvalGT95, $percentEvalGT70, $avgEvalPerDept, $avgAge, $salaryByYear
        ) {
            $out = fopen('php://output', 'w');

            // Cabecera
            fputcsv($out, ['Reporte de Estadísticas de Empleados', 'Generado: ' . now()->toDateTimeString()]);
            fputcsv($out, []);

            // Financieros
            fputcsv($out, ['Financieros']);
            fputcsv($out, ['Promedio salario base', number_format($avgSalaryOverall, 2)]);
            fputcsv($out, ['Total bonificaciones', number_format($totalBonuses, 2)]);
            fputcsv($out, ['Total descuentos', number_format($totalDiscounts, 2)]);
            fputcsv($out, []);

            fputcsv($out, ['Promedio salario por departamento']);
            fputcsv($out, ['Departamento', 'Promedio salario']);
            foreach ($avgSalaryPerDept as $r) {
                fputcsv($out, [(string)$r->departamento, number_format($r->avg_salary, 2)]);
            }
            fputcsv($out, []);

            // Demográficos y desempeño
            fputcsv($out, ['Demográficos']);
            fputcsv($out, ['Edad promedio', number_format($avgAge, 1)]);
            fputcsv($out, []);

            fputcsv($out, ['Desempeño']);
            fputcsv($out, ['Evaluación promedio (global)', number_format($globalAvgEvaluation, 2)]);
            fputcsv($out, ['Empleados con evaluación > 95', $employeesWithEvalGT95]);
            fputcsv($out, ['% personal con evaluación > 70', $percentEvalGT70]);
            fputcsv($out, []);

            fputcsv($out, ['Evaluación promedio por departamento']);
            fputcsv($out, ['Departamento', 'Evaluación promedio']);
            foreach ($avgEvalPerDept as $r) {
                fputcsv($out, [(string)$r->departamento, number_format($r->avg_eval, 2)]);
            }
            fputcsv($out, []);

            // Evolución por año
            fputcsv($out, ['Evolución salario promedio por año']);
            fputcsv($out, ['Año', 'Salario promedio']);
            foreach ($salaryByYear as $y) {
                fputcsv($out, [$y->year, number_format($y->avg_salary, 2)]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }


    /**
     * Exportar estadísticas en PDF (descarga) — usa barryvdh/laravel-dompdf si está instalado.
     */
    public function exportPdf(Request $request)
    {
        // Recalcular métricas (mismo conjunto de datos que en statistics)
        $avgSalaryOverall = (float) round(Empleado::where('estado', 1)->avg('salario_base') ?? 0, 2);
        $avgSalaryPerDept = Empleado::where('estado', 1)
            ->selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(salario_base) as avg_salary')
            ->groupBy('departamento')
            ->get();

        $totalBonuses = (float) Empleado::where('estado', 1)->sum('bonificacion');
        $totalDiscounts = (float) Empleado::where('estado', 1)->sum('descuento');

        $evalRows = Empleado::whereNotNull('evaluacion_desempeno')->get();
        $employeesWithEvalGT95 = Empleado::where('evaluacion_desempeno', '>', 95)->count();
        $totalEvalCount = $evalRows->count();
        $percentEvalGT70 = $totalEvalCount ? round(($evalRows->where('evaluacion_desempeno', '>', 70)->count() / $totalEvalCount) * 100, 1) : 0.0;

        $globalAvgEvaluation = $evalRows->count() ? round($evalRows->avg('evaluacion_desempeno'), 2) : 0.0;

        $ages = Empleado::whereNotNull('fecha_nacimiento')->get()->map(function ($e) {
            try {
                return \Carbon\Carbon::parse($e->fecha_nacimiento)->age;
            } catch (\Exception $ex) {
                return null;
            }
        })->filter();
        $avgAge = $ages->count() ? round($ages->average(), 1) : 0.0;

        $avgEvalPerDept = Empleado::selectRaw('COALESCE(departamento, "Sin asignar") as departamento, AVG(evaluacion_desempeno) as avg_eval')
            ->groupBy('departamento')
            ->get();

        $salaryByYear = Empleado::where('estado', 1)->whereNotNull('fecha_contratacion')
            ->selectRaw('YEAR(fecha_contratacion) as year, AVG(salario_base) as avg_salary')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $data = compact(
            'avgSalaryOverall', 'avgSalaryPerDept', 'totalBonuses', 'totalDiscounts',
            'globalAvgEvaluation', 'employeesWithEvalGT95', 'percentEvalGT70', 'avgEvalPerDept', 'avgAge', 'salaryByYear'
        );

        // Intentar generar PDF usando dompdf. Primero comprobar el binding, luego la fachada
        // y finalmente hacer fallback a HTML descargable si no hay generador disponible.
        $filename = 'report_estadisticas_' . now()->format('Ymd_His');

        // 1) binding 'dompdf.wrapper' (instalación típica del paquete)
        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('empleados.statistics_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->download($filename . '.pdf');
        }

        // 2) intentar usar la fachada si está disponible (evita errores si no está ligada)
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('empleados.statistics_pdf', $data)->setPaper('a4', 'portrait');
            return $pdf->download($filename . '.pdf');
        }

        // 3) Fallback: generar HTML y forzar descarga como archivo .html para que el usuario
        // pueda abrirlo en el navegador y "Imprimir -> Guardar como PDF".
        $html = view('empleados.statistics_pdf', $data)->render();
        $filenameHtml = $filename . '.html';

        return response()->streamDownload(function() use ($html) {
            echo $html;
        }, $filenameHtml, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }


    protected function appendChartData(array $data = [])
    {
        
    }


    public function create()
    {
        return view('empleados.create');
    }


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
         

        $data['bonificacion'] = $data['bonificacion'] ?? 0.00;
        $data['descuento'] = $data['descuento'] ?? 0.00;
        $data['evaluacion_desempeno'] = $data['evaluacion_desempeno'] ?? 0.00;
        $data['estado'] = $data['estado'] ?? 1;

        $resultado_edad = Carbon::parse($data['fecha_nacimiento'])->diffInYears(Carbon::now());

        if (!empty($data['fecha_nacimiento']) && !empty($data['sexo'])) {

            
            $fechaNacimiento = Carbon::parse($data['fecha_nacimiento']);
            $fechaContratacion = Carbon::parse($data['fecha_contratacion']);

                    
                $años_empleado = $fechaNacimiento->diffInYears($fechaContratacion);

                    if ($años_empleado >= 65 && $data['sexo'] == 'M' && $data['sexo'] =='O') {
                        return redirect()->back()
                        ->withInput()
                        ->withErrors(['fecha_nacimiento' => 'No se puede crear un empleado sus años no son aptos para el trabajo.']);
                    } 
                    else if ($años_empleado >= 60 && $data['sexo'] == 'F' && $resultado_edad<18) {
                        return redirect()->back()
                        ->withInput()
                        ->withErrors(['fecha_nacimiento' => 'No se puede crear un empleado sus años no son aptos para el trabajo.']);
                    }else if($resultado_edad<18){
                        return redirect()->back()
                        ->withInput()
                        ->withErrors(['fecha_nacimiento' => 'No se puede crear un empleado menor de edad.']);
                    }
        }

        Empleado::create($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    public function show($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('empleados.show', compact('empleado'));
    }

    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        return view('empleados.edit', compact('empleado'));
    }


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

        $resultado_edad = Carbon::parse($data['fecha_nacimiento'])->diffInYears(Carbon::now());

        $apto= Carbon::parse(Date('now'));


         if (!empty($data['fecha_nacimiento']) && !empty($data['sexo']) ) {
            $fechaNacimiento = Carbon::parse($data['fecha_nacimiento']);
            $fechaContratacion = Carbon::parse($data['fecha_contratacion']);
                    
            $años_empleado = $fechaNacimiento->diffInYears($fechaContratacion);

            if ($años_empleado >= 65 && $data['sexo'] == 'M' && $data['sexo'] =='O' || $resultado_edad<18) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fecha_nacimiento' => 'No se puede actualizar un empleado sus años no son aptos para el trabajo.']);
            } 
            else if ($años_empleado >= 60 && $data['sexo'] == 'F' ) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fecha_nacimiento' => 'No se puede actualizar una empleada sus años no son aptos para el trabajo.']);
            }
        }
        $empleado->update($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }


    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->estado = 0;
        $empleado->save();

        return redirect()->route('empleados.index')->with('success', 'Empleado marcado como inactivo.');
    }
    
}
