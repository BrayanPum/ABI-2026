@extends('tablar::page')

@section('title', 'Proyecciones - Estudiantes')

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
                            <li class="breadcrumb-item active" aria-current="page">Estudiantes</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Soporte de estudiantes PG1 / PG2</h2>
                    <p class="text-muted mb-0">Vista administrativa para entender la base estudiantil que alimenta la proyeccion de continuidad a PG2.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('projections.students.index') }}" class="row g-3 align-items-end">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <div class="col-12 col-lg-3">
                            <label for="academic_period_id" class="form-label">Periodo academico</label>
                            <select id="academic_period_id" name="academic_period_id" class="form-select">
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}" {{ (int) $selectedPeriodId === (int) $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-3">
                            <label for="program_id" class="form-label">Programa</label>
                            <select id="program_id" name="program_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ (int) $selectedProgramId === (int) $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}{{ $program->code ? ' (' . $program->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-2">
                            <label for="stage" class="form-label">Etapa PG</label>
                            <select id="stage" name="stage" class="form-select">
                                <option value="">Todas</option>
                                @foreach($stageOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $selectedStage === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-3">
                            <label for="state" class="form-label">Estado usuario</label>
                            <select id="state" name="state" class="form-select">
                                <option value="">Todos</option>
                                <option value="1" {{ (string) $selectedState === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ (string) $selectedState === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                        </div>
                        <div class="col-12 col-lg-2 d-grid">
                            <a href="{{ route('projections.students.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row row-deck row-cards mb-3">
                <div class="col-md-3">
                    <div class="card"><div class="card-body"><div class="text-muted">Estudiantes filtrados</div><div class="h1 mb-0">{{ $summary['total_students'] }}</div></div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body"><div class="text-muted">Activos</div><div class="h1 mb-0">{{ $summary['active_students'] }}</div></div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body"><div class="text-muted">PG1 actual</div><div class="h1 mb-0">{{ $summary['pg1_students'] }}</div></div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body"><div class="text-muted">PG2 actual</div><div class="h1 mb-0">{{ $summary['pg2_students'] }}</div></div></div>
                </div>
                <div class="col-md-3">
                    <div class="card"><div class="card-body"><div class="text-muted">Proyeccion automatica a PG2</div><div class="h1 mb-0">{{ $summary['projected_pg2_students'] }}</div></div></div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Programa</th>
                                <th>Estado</th>
                                <th>Etapa PG</th>
                                <th>Proyecto actual</th>
                                <th>Periodo base</th>
                                <th>Docentes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $row['full_name'] }}</div>
                                        <div class="text-muted small">Documento: {{ $row['card_id'] }} | Semestre: {{ $row['semester'] }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $row['program_name'] }}</div>
                                        <div class="text-muted small">{{ $row['city_name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $row['is_active'] ? 'bg-green-lt' : 'bg-red-lt' }}">
                                            {{ $row['is_active'] ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $row['pg_stage_label'] }}</div>
                                        <div class="text-muted small">{{ $row['progression_note'] }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $row['project_title'] ?: 'Sin proyecto' }}</div>
                                        <div class="text-muted small">{{ $row['project_status'] ?: 'Sin estado' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $row['assignment_period_name'] ?: 'No aplica' }}</div>
                                        <div class="text-muted small">Corte: {{ $row['reference_period_name'] ?: 'Sin periodo' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $row['teacher_names'] ?: 'Sin docentes asociados' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay estudiantes para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($rows->hasPages())
                    <div class="card-footer">
                        {{ $rows->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <div class="card mt-3" id="students-report">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Reporte de base estudiantil</h3>
                        <div class="text-muted">{{ $reportModules[$activeReportKey]['description'] ?? 'Resumen de la base estudiantil para el periodo seleccionado.' }}</div>
                    </div>
                </div>
                <div class="card-body project-report-shell">
                    <form method="GET" action="{{ route('projections.students.index') }}#students-report" class="row g-3 align-items-end">
                        <input type="hidden" name="academic_period_id" value="{{ $selectedPeriodId }}">
                        <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                        <input type="hidden" name="stage" value="{{ $selectedStage }}">
                        <input type="hidden" name="state" value="{{ $selectedState }}">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
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
                                <a href="{{ route('projections.students.index', array_filter([
                                    'academic_period_id' => $selectedPeriodId,
                                    'program_id' => $selectedProgramId,
                                    'stage' => $selectedStage,
                                    'state' => $selectedState,
                                    'per_page' => $perPage,
                                    'report_key' => $activeReportKey,
                                    'report_export' => 'pdf',
                                ], static fn ($value) => $value !== null && $value !== '')) }}" class="btn btn-outline-danger">Exportar PDF</a>
                            </div>
                        </div>
                    </form>

                    <div class="text-muted small">
                        El reporte usa el periodo academico escogido como corte de lectura para no mezclar continuidad, etapa ni estado entre periodos distintos.
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
                                'groupId' => 'students-report-' . $loop->index,
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
