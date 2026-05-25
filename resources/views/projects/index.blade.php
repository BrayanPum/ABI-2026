@extends('tablar::page')

@section('title', 'Gestion de Proyectos')

@section('content')
    <style>
        .project-report-shell {
            display: grid;
            gap: 1.5rem;
        }

        .project-report-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: space-between;
            align-items: center;
        }

        .project-report-switch {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .project-report-switch__button {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .project-report-switch__button.is-active {
            background: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(15, 118, 110, 0.18);
        }

        .project-report-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .project-report-visuals {
            display: grid;
            gap: 1.5rem;
        }

        .project-report-stat {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .project-report-stat__label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .project-report-stat__value {
            margin-top: 0.35rem;
            font-size: 1.9rem;
            font-weight: 700;
            color: #0f172a;
        }

        .project-report-visual {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
            align-items: start;
        }

        .project-report-panel-wrap {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            padding: 1.25rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
            min-height: 360px;
            display: grid;
            overflow: hidden;
        }

        .project-report-panel {
            display: none;
            height: 100%;
            min-width: 0;
        }

        .project-report-panel.is-active {
            display: grid;
        }

        .project-report-donut-wrap {
            display: grid;
            place-items: center;
            gap: 1rem;
            height: 100%;
        }

        .project-report-donut {
            width: 240px;
            height: 240px;
            border-radius: 50%;
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
        }

        .project-report-donut::after {
            content: '';
            position: absolute;
            inset: 48px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .project-report-donut__center {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            text-align: center;
            z-index: 1;
            padding: 0 1.5rem;
        }

        .project-report-donut__center strong {
            display: block;
            font-size: 2rem;
            color: #0f172a;
        }

        .project-report-donut__center span {
            color: #64748b;
            font-size: 0.9rem;
        }

        .project-report-columns {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(88px, 1fr);
            align-items: end;
            justify-content: stretch;
            justify-items: center;
            gap: 1rem;
            min-height: 260px;
            padding: 0 0.5rem 0.75rem;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .project-report-column {
            width: 100%;
            min-width: 88px;
            max-width: 120px;
            display: grid;
            gap: 0.75rem;
            justify-items: center;
            align-self: end;
        }

        .project-report-column__value {
            font-weight: 700;
            color: #0f172a;
        }

        .project-report-column__bar {
            width: 100%;
            max-width: 72px;
            min-height: 14px;
            border-radius: 18px 18px 0 0;
            display: flex;
            align-items: start;
            justify-content: center;
            padding: 0.5rem 0.35rem 0;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            box-shadow: 0 18px 30px rgba(15, 23, 42, 0.18);
        }

        .project-report-column__label {
            width: 100%;
            text-align: center;
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.3;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .project-report-rows {
            display: grid;
            align-content: center;
            gap: 1rem;
        }

        .project-report-row {
            display: grid;
            gap: 0.45rem;
        }

        .project-report-row__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            color: #334155;
            font-weight: 600;
        }

        .project-report-row__track {
            height: 18px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .project-report-row__fill {
            height: 100%;
            border-radius: inherit;
            min-width: 0;
        }

        .project-report-legend {
            display: grid;
            gap: 0.75rem;
        }

        .project-report-legend__item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 0.75rem;
            align-items: center;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            background: #f8fafc;
        }

        .project-report-legend__swatch {
            width: 0.9rem;
            height: 0.9rem;
            border-radius: 999px;
        }

        .project-report-empty {
            display: grid;
            place-items: center;
            text-align: center;
            color: #64748b;
            min-height: 220px;
        }

        .project-report-table-card {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
        }

        .project-report-table-card .table td {
            vertical-align: top;
        }

        @media (max-width: 991px) {
            .project-report-visual {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575px) {
            .project-report-columns {
                gap: 0.75rem;
            }

            .project-report-column__bar {
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
                            <li class="breadcrumb-item active" aria-current="page">Proyectos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 21v-13l8 -4l8 4v13" />
                            <path d="M12 13l8 -4" />
                            <path d="M12 13l-8 -4" />
                            <path d="M12 13v8" />
                            <path d="M8 21h8" />
                        </svg>
                        Gestion de Proyectos
                    </h2>
                    <p class="text-muted mb-0">Consulta tus proyectos y registra nuevas ideas.</p>
                </div>
                @if ($isProfessor || ($isStudent && $enableButtonStudent))
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            @if ($canCreateProject)
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Nuevo proyecto
                                </a>
                            @else
                                <button type="button" class="btn btn-primary" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    Nuevo proyecto
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($isProfessor || $isStudent)
                <div class="alert {{ $proposalWindowOpen ? 'alert-info' : 'alert-warning' }} mb-3">
                    <strong>Ventana de propuesta:</strong>
                    @if ($proposalWindowOpen && $proposalWindow)
                        {{ optional($activeAcademicPeriod)->name ?? 'Periodo activo' }} -
                        {{ optional($proposalWindow->start_at)->format('d/m/Y H:i') }} a {{ optional($proposalWindow->end_at)->format('d/m/Y H:i') }}.
                    @else
                        {{ $proposalWindowMessage }}
                    @endif
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Buscar proyectos</h3>
                    @if ($isResearchStaff)
                        <a href="{{ route('projects.index', array_merge(request()->except('page'), ['pending_review_due_to_age' => 1])) }}" class="btn btn-outline-warning btn-sm">
                            Ver pendientes por antiguedad
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        @if ($isResearchStaff)
                            <input type="hidden" name="report_key" value="{{ $reportFilters['report_key'] }}">
                            <input type="hidden" name="report_from" value="{{ $reportFilters['report_from'] }}">
                            <input type="hidden" name="report_to" value="{{ $reportFilters['report_to'] }}">
                            <input type="hidden" name="report_program_id" value="{{ $reportFilters['report_program_id'] }}">
                        @endif
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="search" class="form-label">Titulo</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="10" cy="10" r="7" />
                                        <line x1="21" y1="21" x2="15" y2="15" />
                                    </svg>
                                </span>
                                <input type="search" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Titulo del proyecto">
                            </div>
                        </div>
                        @if ($isResearchStaff)
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="city_program_id" class="form-label">Programa - Ciudad</label>
                                <select name="city_program_id" id="city_program_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach ($cityPrograms as $cp)
                                        <option value="{{ $cp->id }}" {{ (string) $selectedCityProgram === (string) $cp->id ? 'selected' : '' }}>
                                            {{ $cp->program->name }} - {{ $cp->city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="status_id" class="form-label">Estado</label>
                            <select name="status_id" id="status_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos los estados</option>
                                @foreach ($projectStatuses as $status)
                                    <option value="{{ $status->id }}" {{ (string) $selectedStatus === (string) $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($isResearchStaff)
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label d-block">Alertas de revision</label>
                                <label class="form-check mb-0">
                                    <input type="checkbox" class="form-check-input" name="pending_review_due_to_age" value="1" {{ $pendingReviewDueToAge ? 'checked' : '' }}>
                                    <span class="form-check-label">Mostrar solo pendientes de revision por antiguedad</span>
                                </label>
                            </div>
                        @endif
                        <div class="col-12 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-primary w-100">Aplicar filtros</button>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Listado de proyectos</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure">{{ $projects->total() }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter align-middle">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th>Titulo</th>
                                <th>Area tematica</th>
                                <th>Estado</th>
                                <th>Profesores</th>
                                <th>Estudiantes</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td class="text-break">{{ $project->title }}</td>
                                    <td>{{ $project->thematicArea->name ?? 'Sin area' }}</td>
                                    @php
                                        $statusName = $project->projectStatus->name ?? 'Sin estado';
                                        $normalizedStatus = \Illuminate\Support\Str::of($statusName)->ascii()->lower()->squish()->toString();
                                        $statusClasses = [
                                            'pendiente de aprobacion' => 'bg-warning text-dark',
                                            'devuelto para correccion' => 'bg-danger text-white',
                                            'aprobado' => 'bg-success text-white',
                                            'waiting evaluation' => 'bg-primary text-white',
                                        ];
                                        $badgeClass = $statusClasses[$normalizedStatus] ?? 'bg-secondary text-white';
                                    @endphp
                                    <td>
                                        <div class="d-flex flex-column gap-1 align-items-start">
                                            <span class="badge {{ $badgeClass }}">{{ $statusName }}</span>
                                            @if ($project->pending_review_due_to_age)
                                                <span class="badge bg-orange-lt text-orange">Pendiente de revision por antiguedad</span>
                                                @if ($project->elapsed_periods_since_proposal !== null)
                                                    <small class="text-secondary">{{ $project->elapsed_periods_since_proposal }} periodos transcurridos</small>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @forelse ($project->professors as $professor)
                                            <div>{{ $professor->name }} {{ $professor->last_name }}</div>
                                        @empty
                                            <span class="text-secondary">Sin profesores</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse ($project->students as $student)
                                            <div>{{ $student->name }} {{ $student->last_name }}</div>
                                        @empty
                                            <span class="text-secondary">Sin estudiantes</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">Ver</a>
                                            @if ($normalizedStatus === 'devuelto para correccion' && ! $isResearchStaff)
                                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">
                                                    Editar
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary">No se encontraron proyectos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-column flex-lg-row align-items-center justify-content-between gap-2">
                    <div class="text-secondary mb-2 mb-lg-0">Mostrando {{ $projects->firstItem() ?? 0 }} a {{ $projects->lastItem() ?? 0 }} de {{ $projects->total() }} registros</div>
                    @if ($projects->hasPages())
                        {{ $projects->links('vendor.pagination.bootstrap-5-numeric') }}
                    @endif
                </div>
            </div>

            @if ($isResearchStaff)
                <div class="card mt-3" id="projects-report">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Reporte de proyectos</h3>
                            <div class="text-muted">{{ $reportModules[$activeReportKey]['description'] ?? 'Distribucion de proyectos.' }}</div>
                        </div>
                    </div>
                    <div class="card-body project-report-shell">
                        <form method="GET" action="{{ route('projects.index') }}#projects-report" class="row g-3 align-items-end">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <input type="hidden" name="status_id" value="{{ $selectedStatus }}">
                            <input type="hidden" name="city_program_id" value="{{ $selectedCityProgram }}">
                            <input type="hidden" name="pending_review_due_to_age" value="{{ $pendingReviewDueToAge ? 1 : '' }}">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="report_key" class="form-label">Que deseas comparar</label>
                                <select id="report_key" name="report_key" class="form-select">
                                    @foreach ($reportModules as $reportKey => $module)
                                        <option value="{{ $reportKey }}" @selected($activeReportKey === $reportKey)>
                                            {{ $module['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 col-lg-2">
                                <label for="report_from" class="form-label">Desde</label>
                                <input
                                    type="date"
                                    id="report_from"
                                    name="report_from"
                                    class="form-control"
                                    value="{{ $reportFilters['report_from'] }}"
                                >
                            </div>
                            <div class="col-12 col-md-4 col-lg-2">
                                <label for="report_to" class="form-label">Hasta</label>
                                <input
                                    type="date"
                                    id="report_to"
                                    name="report_to"
                                    class="form-control"
                                    value="{{ $reportFilters['report_to'] }}"
                                >
                            </div>
                            <div class="col-12 col-md-4 col-lg-4">
                                <label for="report_program_id" class="form-label">Programa</label>
                                <select id="report_program_id" name="report_program_id" class="form-select">
                                    <option value="">Todos los programas</option>
                                    @foreach ($reportProgramOptions as $program)
                                        <option value="{{ $program->id }}" @selected((int) $reportFilters['report_program_id'] === (int) $program->id)>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary">Generar reporte</button>
                                    <a href="{{ route('projects.index') }}#projects-report" class="btn btn-outline-secondary">Limpiar</a>
                                    <a
                                        href="{{ route('projects.index', array_filter([
                                            'search' => $search,
                                            'status_id' => $selectedStatus,
                                            'city_program_id' => $selectedCityProgram,
                                            'pending_review_due_to_age' => $pendingReviewDueToAge ? 1 : null,
                                            'report_key' => $activeReportKey,
                                            'report_from' => $reportFilters['report_from'],
                                            'report_to' => $reportFilters['report_to'],
                                            'report_program_id' => $reportFilters['report_program_id'],
                                            'report_export' => 'csv',
                                        ], static fn ($value) => $value !== null && $value !== '')) }}"
                                        class="btn btn-outline-primary"
                                    >
                                        Exportar CSV
                                    </a>
                                    <a
                                        href="{{ route('projects.index', array_filter([
                                            'search' => $search,
                                            'status_id' => $selectedStatus,
                                            'city_program_id' => $selectedCityProgram,
                                            'pending_review_due_to_age' => $pendingReviewDueToAge ? 1 : null,
                                            'report_key' => $activeReportKey,
                                            'report_from' => $reportFilters['report_from'],
                                            'report_to' => $reportFilters['report_to'],
                                            'report_program_id' => $reportFilters['report_program_id'],
                                            'report_export' => 'pdf',
                                        ], static fn ($value) => $value !== null && $value !== '')) }}"
                                        class="btn btn-outline-danger"
                                    >
                                        Exportar PDF
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="text-muted small">
                            Cada diagrama tiene sus propios controles para cambiar entre dona, barras verticales y barras horizontales.
                        </div>

                        <div class="project-report-grid">
                            @forelse ($reportInsights as $insight)
                                <div class="project-report-stat">
                                    <div class="project-report-stat__label">{{ $insight['label'] }}</div>
                                    <div class="project-report-stat__value" style="font-size: 1.2rem;">
                                        {{ $insight['value'] }}
                                    </div>
                                    <div class="text-muted small mt-2">{{ $insight['caption'] }}</div>
                                </div>
                            @empty
                                <div class="project-report-stat">
                                    <div class="project-report-stat__label">Total de registros</div>
                                    <div class="project-report-stat__value">{{ $reportData['total'] }}</div>
                                </div>
                            @endforelse
                        </div>

                        <div class="project-report-visuals">
                            @forelse ($reportVisuals as $visual)
                                @include('projects.partials.report-visual', [
                                    'visual' => $visual,
                                    'groupId' => 'report-' . $loop->index,
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
            @endif
        </div>
    </div>
    @if ($isResearchStaff)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buttons = Array.from(document.querySelectorAll('[data-chart-target][data-chart-group]'));

                if (buttons.length === 0) {
                    return;
                }

                buttons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        const group = button.getAttribute('data-chart-group');
                        const target = button.getAttribute('data-chart-target');
                        const groupButtons = Array.from(document.querySelectorAll('[data-chart-target][data-chart-group="' + group + '"]'));
                        const groupPanels = Array.from(document.querySelectorAll('[data-chart-panel][data-chart-group="' + group + '"]'));

                        groupButtons.forEach(function (item) {
                            item.classList.toggle('is-active', item === button);
                        });

                        groupPanels.forEach(function (panel) {
                            panel.classList.toggle('is-active', panel.getAttribute('data-chart-panel') === target);
                        });
                    });
                });
            });
        </script>
    @endif
@endsection
