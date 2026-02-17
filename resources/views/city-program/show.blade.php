{{--
    View path: city-program/show.blade.php.
    Purpose: Displays the details for a single city-program assignment.
    Expected variables: $cityProgram.
--}}
@extends('tablar::page')

@section('title', 'Detalle de relación ciudad - programa')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('city-program.index') }}">Ciudad - Programa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Relación #{{ $cityProgram->id }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 21v-13l9 -4l9 4v13" />
                            <path d="M9 13l6 0" />
                            <path d="M9 17l6 0" />
                        </svg>
                        Relación ciudad - programa
                    </h2>
                    <p class="text-muted mb-0">Consulta la información detallada de esta asignación académica.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('city-program.edit', $cityProgram) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10 -10a2.828 2.828 0 1 0 -4 -4l-10 10v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('city-program.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Información principal</h3>
                            <span class="badge bg-indigo-lt">ID {{ $cityProgram->id }}</span>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Ciudad</dt>
                                <dd class="col-sm-7">{{ $cityProgram->city->name ?? 'No asignada' }}</dd>

                                <dt class="col-sm-5 text-muted">Programa</dt>
                                <dd class="col-sm-7">{{ $cityProgram->program->name ?? 'No asignado' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Actividad del registro</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Creado</dt>
                                <dd class="col-sm-7">{{ $cityProgram->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Actualizado</dt>
                                <dd class="col-sm-7">{{ $cityProgram->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </dl>

                            <div class="mt-4 d-flex gap-2 flex-wrap">
                                @if($cityProgram->city)
                                    <a href="{{ route('cities.show', $cityProgram->city_id) }}" class="btn btn-sm btn-outline-primary">
                                        Ver ciudad
                                    </a>
                                @else
                                    <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true">Ver ciudad</span>
                                @endif

                                @if($cityProgram->program)
                                    <a href="{{ route('programs.show', $cityProgram->program_id) }}" class="btn btn-sm btn-outline-primary">
                                        Ver programa
                                    </a>
                                @else
                                    <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true">Ver programa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
