@extends('tablar::page')

@section('title', 'Evaluacion de proyectos')

@section('content')
    @php
        $periodSegments = $reportState['periods'] ?? [];
        $statusSegments = $reportState['statuses'] ?? [];
        $rateSegments = $reportState['rates'] ?? [];
        $totalEvaluated = $reportState['totals']['evaluated'] ?? 0;
        $pendingProjects = $reportState['totals']['pending'] ?? 0;
        $topPeriod = $reportState['topPeriod'] ?? 'Sin datos';
        $maxPeriodValue = max(array_column($periodSegments, 'value') ?: [0]);

        $currentPercent = 0;
        $donutStops = [];

        foreach ($statusSegments as $segment) {
            $start = $currentPercent;
            $currentPercent = min(100, $currentPercent + ($segment['percentage'] ?? 0));
            $donutStops[] = "{$segment['color']} {$start}% {$currentPercent}%";
        }

        $donutBackground = $donutStops !== []
            ? 'conic-gradient(' . implode(', ', $donutStops) . ')'
            : 'linear-gradient(135deg, #cbd5e1, #94a3b8)';
    @endphp

    <style>
        .committee-report-shell {
            display: grid;
            gap: 1.5rem;
        }

        .committee-stat-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .committee-stat-card {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            padding: 1.15rem 1.25rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        }

        .committee-stat-card__label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .committee-stat-card__value {
            margin-top: 0.45rem;
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .committee-stat-card__hint {
            margin-top: 0.55rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .committee-chart-card {
            height: 100%;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
        }

        .committee-chart-card__body {
            padding: 1.25rem;
            display: grid;
            gap: 1rem;
            height: 100%;
        }

        .committee-chart-card__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .committee-chart-card__subtitle {
            color: #64748b;
            font-size: 0.92rem;
        }

        .committee-period-chart {
            display: flex;
            align-items: end;
            justify-content: center;
            gap: 0.9rem;
            min-height: 280px;
            padding: 0.5rem 0.25rem 0;
        }

        .committee-period-chart__item {
            flex: 1;
            min-width: 0;
            display: grid;
            gap: 0.75rem;
            justify-items: center;
            align-items: end;
        }

        .committee-period-chart__value {
            font-weight: 700;
            color: #0f172a;
        }

        .committee-period-chart__bar {
            width: min(84px, 100%);
            min-height: 18px;
            border-radius: 20px 20px 0 0;
            display: flex;
            align-items: start;
            justify-content: center;
            padding: 0.5rem 0.35rem 0;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 700;
            box-shadow: 0 20px 28px rgba(15, 23, 42, 0.18);
        }

        .committee-period-chart__label {
            text-align: center;
            color: #475569;
            font-size: 0.85rem;
            line-height: 1.35;
        }

        .committee-status-layout {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: minmax(220px, 260px) minmax(0, 1fr);
            align-items: center;
        }

        .committee-status-donut-wrap {
            display: grid;
            place-items: center;
            gap: 0.9rem;
        }

        .committee-status-donut {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .committee-status-donut::after {
            content: '';
            position: absolute;
            inset: 44px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .committee-status-donut__center {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 0 1rem;
        }

        .committee-status-donut__center strong {
            display: block;
            font-size: 1.9rem;
            color: #0f172a;
            line-height: 1;
        }

        .committee-status-donut__center span {
            color: #64748b;
            font-size: 0.9rem;
        }

        .committee-status-legend {
            display: grid;
            gap: 0.8rem;
        }

        .committee-status-legend__item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 0.8rem;
            align-items: center;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            background: #f8fafc;
        }

        .committee-status-legend__swatch {
            width: 0.9rem;
            height: 0.9rem;
            border-radius: 999px;
        }

        .committee-rate-list {
            display: grid;
            gap: 1rem;
        }

        .committee-rate-item {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 16px;
            padding: 1rem 1.1rem;
            background: #ffffff;
        }

        .committee-rate-item__header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.7rem;
        }

        .committee-rate-item__title {
            font-weight: 700;
            color: #0f172a;
        }

        .committee-rate-item__meta {
            color: #334155;
            font-weight: 600;
        }

        .committee-rate-item__track {
            height: 16px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .committee-rate-item__fill {
            height: 100%;
            border-radius: inherit;
        }

        .committee-rate-item__hint {
            margin-top: 0.65rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .committee-empty-state {
            min-height: 220px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 991px) {
            .committee-status-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .committee-period-chart {
                gap: 0.65rem;
            }

            .committee-period-chart__bar {
                width: 100%;
            }
        }
    </style>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Evaluacion de proyectos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <path d="M8 12h8M8 8h8M8 16h8" />
                        </svg>
                        Reporte de comite
                        <span class="badge bg-indigo ms-2">{{ $pendingProjects }}</span>
                    </h2>
                    <p class="text-muted mb-0">
                        Analiza las ideas evaluadas y los pendientes del programa
                        {{ $committeeLeader->cityProgram->program->name ?? 'asignado' }}
                        @if (! empty($committeeLeader->cityProgram->city->name))
                            en {{ $committeeLeader->cityProgram->city->name }}
                        @endif
                        .
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <div class="committee-report-shell">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Proyectos pendientes por revisar</h3>
                            <div class="text-muted">Listado operativo para continuar con la evaluacion del programa.</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Titulo</th>
                                    <th>Area tematica</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Autores</th>
                                    <th class="w-1">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $index => $project)
                                    <tr>
                                        <td class="text-secondary">{{ $index + 1 }}</td>
                                        <td class="fw-semibold text-wrap">{{ $project->title }}</td>
                                        <td>{{ $project->thematicArea->name ?? 'Sin area tematica' }}</td>
                                        <td>
                                            <span class="badge bg-warning-lt text-dark">
                                                {{ $project->projectStatus->name ?? 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td>{{ optional($project->proposed_at ?? $project->created_at)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($project->students as $student)
                                                    <span class="badge bg-blue-lt">{{ $student->name }} {{ $student->last_name }}</span>
                                                @endforeach
                                                @foreach ($project->professors as $professor)
                                                    <span class="badge bg-green-lt">{{ $professor->name }} {{ $professor->last_name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('projects.evaluation.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                                Ver detalles
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                                        <path d="M8 12h8M8 8h8M8 16h8" />
                                                    </svg>
                                                </div>
                                                <p class="empty-title">No hay proyectos pendientes</p>
                                                <p class="empty-subtitle text-muted">Todas las ideas del programa tienen una decision registrada por el comite.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="committee-stat-grid">
                    <div class="committee-stat-card">
                        <div class="committee-stat-card__label">Ideas evaluadas</div>
                        <div class="committee-stat-card__value">{{ $totalEvaluated }}</div>
                        <div class="committee-stat-card__hint">Total historico con concepto final registrado.</div>
                    </div>
                    <div class="committee-stat-card">
                        <div class="committee-stat-card__label">Pendientes actuales</div>
                        <div class="committee-stat-card__value">{{ $pendingProjects }}</div>
                        <div class="committee-stat-card__hint">Proyectos que siguen esperando revision.</div>
                    </div>
                    <div class="committee-stat-card">
                        <div class="committee-stat-card__label">Aprobadas</div>
                        <div class="committee-stat-card__value">{{ $reportState['totals']['approved'] ?? 0 }}</div>
                        <div class="committee-stat-card__hint">Ideas con resultado favorable del comite.</div>
                    </div>
                    <div class="committee-stat-card">
                        <div class="committee-stat-card__label">Periodo mas activo</div>
                        <div class="committee-stat-card__value" style="font-size: 1.35rem;">{{ $topPeriod }}</div>
                        <div class="committee-stat-card__hint">Periodo con mayor numero de ideas evaluadas.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('projects.evaluation.index', ['report_export' => 'pdf']) }}" class="btn btn-outline-danger">
                        Exportar reporte en PDF
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-xl-7">
                        <div class="committee-chart-card">
                            <div class="committee-chart-card__body">
                                <div>
                                    <h3 class="committee-chart-card__title">Ideas evaluadas por periodo</h3>
                                    <div class="committee-chart-card__subtitle">
                                        Cada barra muestra cuantas ideas alcanzaron un resultado final dentro de cada periodo academico.
                                    </div>
                                </div>

                                @if ($periodSegments !== [])
                                    <div class="committee-period-chart">
                                        @foreach ($periodSegments as $period)
                                            @php
                                                $height = $maxPeriodValue > 0
                                                    ? max(18, (int) round(($period['value'] / $maxPeriodValue) * 220))
                                                    : 18;
                                            @endphp
                                            <div class="committee-period-chart__item">
                                                <div class="committee-period-chart__value">{{ $period['value'] }}</div>
                                                <div
                                                    class="committee-period-chart__bar"
                                                    style="height: {{ $height }}px; background: {{ $period['color'] }};"
                                                    title="{{ $period['label'] }}: {{ $period['value'] }} ideas"
                                                >
                                                    {{ number_format($period['percentage'], 1) }}%
                                                </div>
                                                <div class="committee-period-chart__label">{{ $period['label'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="committee-empty-state">
                                        No hay evaluaciones historicas todavia para construir este grafico.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-5">
                        <div class="committee-chart-card">
                            <div class="committee-chart-card__body">
                                <div>
                                    <h3 class="committee-chart-card__title">Estado final de las ideas</h3>
                                    <div class="committee-chart-card__subtitle">
                                        Distribucion entre aprobacion, rechazo y devolucion con base en la ultima decision registrada.
                                    </div>
                                </div>

                                @if ($statusSegments !== [])
                                    <div class="committee-status-layout">
                                        <div class="committee-status-donut-wrap">
                                            <div class="committee-status-donut" style="background: {{ $donutBackground }};">
                                                <div class="committee-status-donut__center">
                                                    <div>
                                                        <strong>{{ $totalEvaluated }}</strong>
                                                        <span>Total evaluadas</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-muted text-center small">
                                                Vista general del resultado final del comite.
                                            </div>
                                        </div>

                                        <div class="committee-status-legend">
                                            @foreach ($statusSegments as $segment)
                                                <div class="committee-status-legend__item">
                                                    <span class="committee-status-legend__swatch" style="background: {{ $segment['color'] }}"></span>
                                                    <div>
                                                        <div class="fw-semibold">{{ $segment['label'] }}</div>
                                                        <div class="text-muted small">{{ $segment['value'] }} ideas</div>
                                                    </div>
                                                    <div class="fw-semibold">{{ number_format($segment['percentage'], 2) }}%</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="committee-empty-state">
                                        No hay resultados finales para construir este grafico.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="committee-chart-card">
                            <div class="committee-chart-card__body">
                                <div>
                                    <h3 class="committee-chart-card__title">Porcentajes de aceptacion, rechazo y devolucion</h3>
                                    <div class="committee-chart-card__subtitle">
                                        Comparativo especifico de las tres salidas principales del proceso de evaluacion del comite.
                                    </div>
                                </div>

                                @if ($rateSegments !== [])
                                    <div class="committee-rate-list">
                                        @foreach ($rateSegments as $rate)
                                            <div class="committee-rate-item">
                                                <div class="committee-rate-item__header">
                                                    <div class="committee-rate-item__title">{{ $rate['label'] }}</div>
                                                    <div class="committee-rate-item__meta">
                                                        {{ $rate['value'] }} ideas · {{ number_format($rate['percentage'], 2) }}%
                                                    </div>
                                                </div>
                                                <div class="committee-rate-item__track">
                                                    <div
                                                        class="committee-rate-item__fill"
                                                        style="width: {{ min(100, max(0, $rate['percentage'])) }}%; background: {{ $rate['color'] }};"
                                                    ></div>
                                                </div>
                                                <div class="committee-rate-item__hint">{{ $rate['description'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="committee-empty-state">
                                        No hay suficientes datos para calcular los porcentajes del comite.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
