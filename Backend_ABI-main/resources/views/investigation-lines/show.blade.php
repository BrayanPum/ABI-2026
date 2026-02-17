{{--
    View path: investigation-lines/show.blade.php.
    Purpose: Renders the show.blade view for the Investigation Lines module.
    Expected variables within this template: $area, $investigationLine.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Detalle de la línea de investigación')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('investigation-lines.index') }}">Líneas de investigación</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-purple" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16" />
                            <path d="M4 12h10" />
                            <path d="M4 18h4" />
                        </svg>
                        {{ $investigationLine->name }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('investigation-lines.edit', $investigationLine) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar línea
                        </a>
                        <a href="{{ route('investigation-lines.index') }}" class="btn btn-outline-secondary">
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
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Nombre</dt>
                                <dd class="col-sm-9">{{ $investigationLine->name }}</dd>

                                <dt class="col-sm-3">Descripción</dt>
                                <dd class="col-sm-9">{{ $investigationLine->description }}</dd>

                                <dt class="col-sm-3">Grupo de investigación</dt>
                                <dd class="col-sm-9">
                                    @if($investigationLine->researchGroup)
                                        <a href="{{ route('research-groups.show', $investigationLine->researchGroup) }}" class="text-decoration-none">
                                            {{ $investigationLine->researchGroup->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Creado</dt>
                                <dd class="col-sm-9">{{ $investigationLine->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</dd>

                                <dt class="col-sm-3">Última actualización</dt>
                                <dd class="col-sm-9">{{ $investigationLine->updated_at?->diffForHumans() ?? 'N/D' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Áreas temáticas asociadas</h3>
                            <span class="badge bg-purple-lt">{{ $investigationLine->thematicAreas->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($investigationLine->thematicAreas->isEmpty())
                                <p class="text-muted mb-0">No hay áreas temáticas registradas para esta línea.</p>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($investigationLine->thematicAreas as $area)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-medium">{{ $area->name }}</div>
                                                    <div class="text-muted small">{{ Str::limit($area->description, 140) }}</div>
                                                </div>
                                                <a href="{{ route('thematic-areas.show', $area) }}" class="btn btn-sm btn-outline-primary">Ver</a>
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
