{{--
    View path: research-groups/show.blade.php.
    Purpose: Renders the show.blade view for the Research Groups module.
    Expected variables within this template: $line, $program, $researchGroup.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Detalle del grupo de investigación')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('research-groups.index') }}">Grupos de investigación</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        {{ $researchGroup->name }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('research-groups.edit', $researchGroup) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar grupo
                        </a>
                        <a href="{{ route('research-groups.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                            <div class="card-actions text-muted small">
                                Actualizado {{ $researchGroup->updated_at?->diffForHumans() ?? 'sin cambios' }}
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Nombre</dt>
                                <dd class="col-sm-9">{{ $researchGroup->name }}</dd>

                                <dt class="col-sm-3">Sigla</dt>
                                <dd class="col-sm-9"><span class="badge bg-indigo-lt">{{ $researchGroup->initials }}</span></dd>

                                <dt class="col-sm-3">Descripción</dt>
                                <dd class="col-sm-9">{{ $researchGroup->description }}</dd>

                                <dt class="col-sm-3">Programas asociados</dt>
                                <dd class="col-sm-9">{{ $researchGroup->programs->count() }}</dd>

                                <dt class="col-sm-3">Líneas de investigación</dt>
                                <dd class="col-sm-9">{{ $researchGroup->investigationLines->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Programas vinculados</h3>
                            <span class="badge bg-azure-lt">{{ $researchGroup->programs->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($researchGroup->programs->isEmpty())
                                <p class="text-muted mb-0">No hay programas asociados todavía.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($researchGroup->programs as $program)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium">{{ $program->name }}</div>
                                                <div class="text-muted small">Código: {{ $program->code }}</div>
                                            </div>
                                            <a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Líneas de investigación</h3>
                            <span class="badge bg-purple-lt">{{ $researchGroup->investigationLines->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($researchGroup->investigationLines->isEmpty())
                                <p class="text-muted mb-0">No hay líneas registradas todavía.</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($researchGroup->investigationLines as $line)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-medium">{{ $line->name }}</div>
                                                    <div class="text-muted small">{{ Str::limit($line->description, 120) }}</div>
                                                </div>
                                                <a href="{{ route('investigation-lines.show', $line) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
