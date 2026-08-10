@extends('tablar::page')

@section('title', 'Demanda de ideas')

@section('content')
    @include('reports.partials.visual-report-styles')

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item">Proyecciones</li>
                            <li class="breadcrumb-item active" aria-current="page">Demanda de ideas</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Demanda de ideas</h2>
                    <p class="text-muted mb-0">Compara la demanda PG1 proyectada con las ideas aprobadas y sin asignar disponibles en el banco.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('projections.idea-demand.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label for="academic_period_id" class="form-label">Periodo objetivo</label>
                            <select id="academic_period_id" name="academic_period_id" class="form-select">
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}" {{ (int) $selectedPeriodId === (int) $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-5">
                            <label for="program_id" class="form-label">Programa</label>
                            <select id="program_id" name="program_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ (int) $selectedProgramId === (int) $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row row-deck row-cards mb-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Programas proyectados</div>
                            <div class="h1 mb-0">{{ $summary['totals']['projected_programs'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Ideas requeridas</div>
                            <div class="h1 mb-0">{{ $summary['totals']['needed_ideas'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Ideas disponibles</div>
                            <div class="h1 mb-0">{{ $summary['totals']['available_ideas'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Ideas faltantes</div>
                            <div class="h1 mb-0">{{ $summary['totals']['missing_ideas'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Programa</th>
                                <th>Periodo</th>
                                <th>Ideas requeridas</th>
                                <th>Ideas disponibles</th>
                                <th>Faltantes</th>
                                <th>Excedente</th>
                                <th>Alertas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['rows'] as $row)
                                <tr>
                                    <td>{{ $row['program']?->name }}</td>
                                    <td>{{ $row['academic_period']?->name }}</td>
                                    <td>{{ $row['needed_ideas'] }}</td>
                                    <td>{{ $row['available_ideas'] }}</td>
                                    <td>{{ $row['missing_ideas'] }}</td>
                                    <td>{{ $row['excess_ideas'] }}</td>
                                    <td>
                                        @if(empty($row['alerts']))
                                            <span class="text-muted">Sin alertas</span>
                                        @else
                                            @foreach(array_slice($row['alerts'], 0, 2) as $alert)
                                                <div class="small text-{{ $alert['level'] === 'danger' ? 'danger' : ($alert['level'] === 'warning' ? 'warning' : 'muted') }}">
                                                    {{ $alert['message'] }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty">
                                            <p class="empty-title">No hay informacion para analizar</p>
                                            <p class="empty-subtitle text-muted">Primero registra proyecciones de carga para el periodo objetivo.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($detailRow)
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Balance por linea</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Linea de investigacion</th>
                                            <th>Ideas disponibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailRow['line_breakdown'] as $line)
                                            <tr>
                                                <td>{{ $line['name'] }}</td>
                                                <td>{{ $line['count'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">Sin ideas aprobadas disponibles para este programa.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Balance por area tematica</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Area</th>
                                            <th>Linea</th>
                                            <th>Ideas disponibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detailRow['area_breakdown'] as $area)
                                            <tr>
                                                <td>{{ $area['name'] }}</td>
                                                <td>{{ $area['line_name'] }}</td>
                                                <td>{{ $area['count'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-muted">Sin datos de areas para este programa.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Alertas de equilibrio</h3>
                            </div>
                            <div class="card-body">
                                @forelse($detailRow['alerts'] as $alert)
                                    <div class="alert alert-{{ $alert['level'] === 'danger' ? 'danger' : ($alert['level'] === 'warning' ? 'warning' : ($alert['level'] === 'success' ? 'success' : 'secondary')) }} mb-2">
                                        {{ $alert['message'] }}
                                    </div>
                                @empty
                                    <div class="text-muted">No se generaron alertas para el programa seleccionado.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mt-3" id="idea-demand-report">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Reporte de demanda de ideas</h3>
                        <div class="text-muted">{{ $reportModules[$activeReportKey]['description'] ?? 'Comparativo del banco institucional para el periodo seleccionado.' }}</div>
                    </div>
                </div>
                <div class="card-body project-report-shell">
                    <form method="GET" action="{{ route('projections.idea-demand.index') }}#idea-demand-report" class="row g-3 align-items-end">
                        <input type="hidden" name="academic_period_id" value="{{ $selectedPeriodId }}">
                        <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                        <div class="col-12 col-lg-6">
                            <label for="report_key" class="form-label">Reporte</label>
                            <select id="report_key" name="report_key" class="form-select">
                                @foreach ($reportModules as $reportKey => $module)
                                    <option value="{{ $reportKey }}" @selected($activeReportKey === $reportKey)>{{ $module['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">Generar reporte</button>
                                <a href="{{ route('projections.idea-demand.index', array_filter([
                                    'academic_period_id' => $selectedPeriodId,
                                    'program_id' => $selectedProgramId,
                                    'report_key' => $activeReportKey,
                                    'report_export' => 'pdf',
                                ], static fn ($value) => $value !== null && $value !== '')) }}" class="btn btn-outline-danger">Exportar PDF</a>
                            </div>
                        </div>
                    </form>

                    <div class="text-muted small">
                        El comparativo se mantiene dentro del periodo academico seleccionado para no mezclar la lectura del banco entre cortes.
                    </div>

                    <div class="project-report-grid">
                        @foreach ($reportInsights as $insight)
                            <div class="project-report-stat">
                                <div class="project-report-stat__label">{{ $insight['label'] }}</div>
                                <div class="project-report-stat__value" style="font-size: 1.2rem;">{{ $insight['value'] }}</div>
                                <div class="text-muted small mt-2">{{ $insight['caption'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="project-report-visuals">
                        @forelse ($reportVisuals as $visual)
                            @include('projects.partials.report-visual', [
                                'visual' => $visual,
                                'groupId' => 'idea-demand-report-' . $loop->index,
                            ])
                        @empty
                            <div class="project-report-empty">Sin datos para construir el reporte.</div>
                        @endforelse
                    </div>

                    @if ($reportTable)
                        <div class="card project-report-table-card bg-white">
                            <div class="card-header">
                                <div>
                                    <h4 class="card-title mb-0">{{ $reportTable['title'] }}</h4>
                                    <div class="text-muted">{{ $reportTable['description'] }}</div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter">
                                    <thead>
                                        <tr>
                                            @foreach ($reportTable['columns'] as $column)
                                                <th>{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reportTable['rows'] as $row)
                                            <tr>
                                                @foreach ($row as $cell)
                                                    <td class="text-break">{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($reportTable['columns']) }}" class="text-center text-secondary">
                                                    No se encontraron registros para este reporte.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('reports.partials.visual-report-scripts')
@endsection
