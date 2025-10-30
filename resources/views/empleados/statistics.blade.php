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

@endsection

@section('content_bottom')
<!-- Additional performance & tenure cards inserted below main content for better layout -->
@endsection

