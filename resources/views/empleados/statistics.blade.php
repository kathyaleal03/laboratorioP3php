@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Estadísticas y Análisis</h2>
    </div>
    <!-- Desempeño y Antigüedad -->
    <div class="row g-4 mt-4">
        <!-- Desempeño -->
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">Desempeño</h5>
                    <div class="row gy-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 p-2">
                                    <i class="bi bi-star-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Evaluación promedio (global)</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($globalAvgEvaluation, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-info text-white rounded-3 p-2">
                                    <i class="bi bi-link-45deg fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Correlación salario-desempeño</div>
                                    <div class="fs-5 fw-semibold">{{ $salaryEvalCorr }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-3 p-2">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Empleados con evaluación &gt; 95</div>
                                    <div class="fs-5 fw-semibold">{{ $employeesWithEvalGT95 }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-warning text-dark rounded-3 p-2">
                                    <i class="bi bi-bar-chart-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Personal con evaluación &gt; 70</div>
                                    <div class="fs-5 fw-semibold">{{ $percentEvalGT70 }}%</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="mt-3">Evaluación promedio por departamento</h6>
                            <div class="list-group list-group-flush">
                                @foreach($avgEvalPerDept as $row)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>{{ $row->departamento }}</div>
                                        <div class="fw-semibold">{{ number_format($row->avg_eval, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antigüedad / Permanencia -->
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">Antigüedad</h5>
                    <div class="row gy-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-secondary text-white rounded-3 p-2">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Antigüedad promedio (años)</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($avgTenure, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 p-2">
                                    <i class="bi bi-clock-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Tiempo promedio de permanencia (años)</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($medianTenure, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-info text-white rounded-3 p-2">
                                    <i class="bi bi-link-45deg fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Correlación antigüedad-salario</div>
                                    <div class="fs-5 fw-semibold">{{ $tenureSalaryCorr }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-danger text-white rounded-3 p-2">
                                    <i class="bi bi-percent fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Personal con más de 10 años</div>
                                    <div class="fs-5 fw-semibold">{{ $percentOver10 }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Financieros -->
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">Financieros</h5>
                    <div class="row gy-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 p-2">
                                    <i class="bi bi-currency-dollar fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Promedio de salario base</div>
                                    <div class="fs-5 fw-semibold">${{ number_format($avgSalaryOverall, 2) }}</div>
                                    <div class="small text-muted">(por empleado)</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-3 p-2">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total de bonificaciones</div>
                                    <div class="fs-5 fw-semibold">${{ number_format($totalBonuses, 2) }}</div>
                                    <div class="small text-muted">(suma)</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-danger text-white rounded-3 p-2">
                                    <i class="bi bi-file-minus fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total de descuentos</div>
                                    <div class="fs-5 fw-semibold">${{ number_format($totalDiscounts, 2) }}</div>
                                    <div class="small text-muted">(suma)</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-info text-white rounded-3 p-2">
                                    <i class="bi bi-graph-up fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Crecimiento salario neto vs año anterior</div>
                                    <div class="fs-5 fw-semibold">{{ $growthPct >= 0 ? '+' : '' }}{{ number_format($growthPct, 2) }}%</div>
                                    <div class="small text-muted">Comparado entre contrataciones por año</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="mt-3">Promedio por departamento</h6>
                            <div class="list-group list-group-flush">
                                @foreach($avgSalaryPerDept as $row)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>{{ $row->departamento }}</div>
                                        <div class="fw-semibold">${{ number_format($row->avg_salary, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demográficos -->
        <div class="col-lg-6">
            <div class="card card-shadow h-100">
                <div class="card-body">
                    <h5 class="card-title">Demográficos</h5>
                    <div class="row gy-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-secondary text-white rounded-3 p-2">
                                    <i class="bi bi-person-lines-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Edad promedio del personal</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($avgAge, 1) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-warning text-dark rounded-3 p-2">
                                    <i class="bi bi-gender-trans fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Distribución por sexo (M / F / O)</div>
                                    <div class="fs-6 fw-semibold">{{ $genderDistribution['M'] }}% / {{ $genderDistribution['F'] }}% / {{ $genderDistribution['O'] }}%</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-3 p-2">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Edad promedio por puesto directivo</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($avgAgeDirectivos, 1) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-3 p-2">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Edad promedio por área operativa</div>
                                    <div class="fs-5 fw-semibold">{{ number_format($avgAgeOperativos, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Visualizaciones recomendadas -->
    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-body">
                    <h6 class="card-title">Salario Promedio por Departamento</h6>
                    <canvas id="chartDept" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-body">
                    <h6 class="card-title">Desempeño vs Salario Base</h6>
                    <canvas id="chartScatter" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-body">
                    <h6 class="card-title">Distribución por Sexo</h6>
                    <canvas id="chartGender" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-body">
                    <h6 class="card-title">Evolución Salario Promedio</h6>
                    <canvas id="chartSalaryEvolution" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos desde PHP
    const deptLabels = @json($chartDeptLabels ?? []);
    const deptData = @json($chartDeptData ?? []);

    const scatterData = @json($chartScatterData ?? []);

    const genderLabels = @json($chartGenderLabels ?? ['M','F','O']);
    const genderData = @json($chartGenderData ?? [0,0,0]);

    const yearsLabels = @json($chartYears ?? []);
    const yearsData = @json($chartYearsData ?? []);

        // Colores
        const palette = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'];

        // Bar chart - Salario promedio por departamento
        const ctxDept = document.getElementById('chartDept').getContext('2d');
        new Chart(ctxDept, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Salario Promedio ($)',
                    data: deptData,
                    backgroundColor: deptLabels.map((_,i)=> palette[i % palette.length]),
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Scatter chart - Desempeño vs salario
        const ctxScatter = document.getElementById('chartScatter').getContext('2d');
        new Chart(ctxScatter, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Empleados',
                    data: scatterData,
                    backgroundColor: 'rgba(231, 74, 59, 0.8)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: 'Salario Base ($)' } },
                    y: { title: { display: true, text: 'Evaluación de Desempeño' }, suggestedMin: 0, suggestedMax: 100 }
                }
            }
        });

        // Pie chart - Distribución por sexo
        const ctxGender = document.getElementById('chartGender').getContext('2d');
        new Chart(ctxGender, {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{ data: genderData, backgroundColor: ['#36b9cc','#e74a3b','#4e73df'] }]
            },
            options: { responsive: true }
        });

        // Line chart - Evolución salario promedio
        const ctxYears = document.getElementById('chartSalaryEvolution').getContext('2d');
        new Chart(ctxYears, {
            type: 'line',
            data: {
                labels: yearsLabels,
                datasets: [{
                    label: 'Salario Promedio ($)',
                    data: yearsData,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28,200,138,0.15)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
        });
    </script>

@endsection

@section('content_bottom')
<!-- Additional performance & tenure cards inserted below main content for better layout -->
@endsection

