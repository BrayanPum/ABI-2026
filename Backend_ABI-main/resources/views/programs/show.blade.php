{{--
    View path: programs/show.blade.php.
    Purpose: Renders the detail view for the Programs module.
    Expected variables within this template: $program.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Detalle del programa académico')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('programs.index') }}">Programas académicos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-teal" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20l-8 -4l8 -4l8 4l-8 4" />
                            <path d="M4 12l8 -4l8 4" />
                        </svg>
                        {{ $program->name }}
                    </h2>
                    <p class="text-muted mb-0">Consulta los detalles del programa y verifica su grupo de investigación asociado.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            Volver al listado
                        </a>
                        <a href="{{ route('programs.edit', $program) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar programa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información general</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $program->name }}</dd>

                        <dt class="col-sm-3">Código</dt>
                        <dd class="col-sm-9"><span class="badge bg-teal-lt">{{ $program->code }}</span></dd>

                        <dt class="col-sm-3">Grupo de investigación</dt>
                        <dd class="col-sm-9">
                            @if($program->researchGroup)
                                <a href="{{ route('research-groups.show', $program->researchGroup) }}" class="text-decoration-none text-reset">
                                    {{ $program->researchGroup->name }}
                                </a>
                            @else
                                <span class="text-muted">Sin grupo asignado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Creado</dt>
                        <dd class="col-sm-9">{{ $program->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</dd>

                        <dt class="col-sm-3">Última actualización</dt>
                        <dd class="col-sm-9">{{ $program->updated_at?->diffForHumans() ?? 'N/D' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
