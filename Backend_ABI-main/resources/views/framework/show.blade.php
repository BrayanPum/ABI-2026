{{--
    View path: framework/show.blade.php.
    Purpose: Renders the Tablar-styled detail screen for an academic framework.
    Expected variables within this template: $framework.
--}}
@extends('tablar::page')

@section('title', 'Detalle del marco')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Marcos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="2" />
                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                        </svg>
                        Marco #{{ $framework->id }}
                    </h2>
                    <p class="text-muted mb-0">Consulta la información vigente del marco curricular y su período de aplicación.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                    <a href="{{ route('frameworks.edit', $framework) }}" class="btn btn-primary">Editar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">{{ $framework->name }}</h3>
                                <small class="text-secondary">Creado {{ $framework->created_at?->diffForHumans() ?? '—' }}</small>
                            </div>
                            @php
                                $currentYear = (int) date('Y');
                                $startYear = (int) $framework->start_year;
                                $endYear = $framework->end_year ? (int) $framework->end_year : null;
                                $isCurrent = $startYear <= $currentYear && ($endYear === null || $endYear >= $currentYear);
                                $isFuture = $startYear > $currentYear;
                            @endphp
                            @if($isCurrent)
                                <span class="badge bg-success-lt">Vigente</span>
                            @elseif($isFuture)
                                <span class="badge bg-info-lt">Programado</span>
                            @else
                                <span class="badge bg-yellow-lt">Finalizado</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <dl class="row g-3 mb-0">
                                <dt class="col-sm-4">Descripción</dt>
                                <dd class="col-sm-8">{{ $framework->description }}</dd>

                                <dt class="col-sm-4">Enlace</dt>
                                <dd class="col-sm-8">
                                    @if(!empty($framework->link))
                                        <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-primary text-break">{{ $framework->link }}</a>
                                    @else
                                        <span class="text-secondary">Sin enlace registrado</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Última actualización</dt>
                                <dd class="col-sm-8">{{ $framework->updated_at?->diffForHumans() ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Período de vigencia</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-secondary small">Año de inicio</div>
                                <span class="badge bg-green-lt fs-6">{{ $framework->start_year }}</span>
                            </div>
                            <div class="mb-3">
                                <div class="text-secondary small">Año de finalización</div>
                                @if($framework->end_year)
                                    <span class="badge bg-red-lt fs-6">{{ $framework->end_year }}</span>
                                @else
                                    <span class="badge bg-blue-lt fs-6">Indefinido</span>
                                @endif
                            </div>
                            @php
                                $timelineEnd = $framework->end_year ? (int) $framework->end_year : $currentYear;
                                $totalYears = max(1, $timelineEnd - $startYear + 1);
                                $elapsedYears = $isFuture ? 0 : max(0, min($totalYears, $currentYear - $startYear + 1));
                                $progress = $totalYears > 0 ? min(100, ($elapsedYears / $totalYears) * 100) : 0;
                            @endphp
                            <div class="text-secondary small mb-1">Progreso</div>
                            <div class="progress progress-lg mb-2">
                                <div class="progress-bar bg-primary" style="width: {{ $progress }}%" role="progressbar"></div>
                            </div>
                            <div class="d-flex justify-content-between text-secondary small">
                                <span>{{ $framework->start_year }}</span>
                                <span>{{ $currentYear }}</span>
                                <span>{{ $framework->end_year ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
