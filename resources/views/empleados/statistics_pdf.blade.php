<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte de Estadísticas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0.2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; }
        th { background: #f4f4f4; text-align: left; }
        .section { margin-top: 10px; margin-bottom: 6px; }
    </style>
</head>
<body>
    <h2>Reporte de Estadísticas de Empleados</h2>
    <div>Generado: {{ now()->toDateTimeString() }}</div>

    <div class="section">
        <h3>Financieros</h3>
        <table>
            <tr><th>Promedio salario base</th><td>${{ number_format($avgSalaryOverall, 2) }}</td></tr>
            <tr><th>Total bonificaciones</th><td>${{ number_format($totalBonuses, 2) }}</td></tr>
            <tr><th>Total descuentos</th><td>${{ number_format($totalDiscounts, 2) }}</td></tr>
        </table>

        <h4>Promedio salario por departamento</h4>
        <table>
            <thead><tr><th>Departamento</th><th>Promedio salario</th></tr></thead>
            <tbody>
                @foreach($avgSalaryPerDept as $r)
                    <tr><td>{{ $r->departamento }}</td><td>${{ number_format($r->avg_salary, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Demográficos</h3>
        <table>
            <tr><th>Edad promedio</th><td>{{ number_format($avgAge, 1) }} años</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Desempeño</h3>
        <table>
            <tr><th>Evaluación promedio (global)</th><td>{{ number_format($globalAvgEvaluation, 2) }}</td></tr>
            <tr><th>Empleados con evaluación &gt; 95</th><td>{{ $employeesWithEvalGT95 }}</td></tr>
            <tr><th>% personal con evaluación &gt; 70</th><td>{{ $percentEvalGT70 }}%</td></tr>
        </table>

        <h4>Evaluación promedio por departamento</h4>
        <table>
            <thead><tr><th>Departamento</th><th>Evaluación promedio</th></tr></thead>
            <tbody>
                @foreach($avgEvalPerDept as $r)
                    <tr><td>{{ $r->departamento }}</td><td>{{ number_format($r->avg_eval, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Evolución salario promedio por año</h3>
        <table>
            <thead><tr><th>Año</th><th>Salario promedio</th></tr></thead>
            <tbody>
                @foreach($salaryByYear as $y)
                    <tr><td>{{ $y->year }}</td><td>${{ number_format($y->avg_salary, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
