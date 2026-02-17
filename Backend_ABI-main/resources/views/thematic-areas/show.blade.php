{{--
    View path: thematic-areas/show.blade.php.
    Purpose: Renders the show.blade view for the Thematic Areas module.
    Expected variables within this template: $thematicArea.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Detalle del área temática')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('thematic-areas.index') }}">Áreas temáticas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-orange" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 21l-7 -18l7 4l7 -4z" />
                        </svg>
                        {{ $thematicArea->name }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('thematic-areas.edit', $thematicArea) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar área
                        </a>
                        <a href="{{ route('thematic-areas.index') }}" class="btn btn-outline-secondary">
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
            <div class="row row-cards justify-content-center">
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                            <div class="card-actions">
                                <span class="badge bg-orange-lt">ID {{ $thematicArea->id }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Nombre</dt>
                                <dd class="col-sm-9">{{ $thematicArea->name }}</dd>

                                <dt class="col-sm-3">Descripción</dt>
                                <dd class="col-sm-9">{{ $thematicArea->description }}</dd>

                                <dt class="col-sm-3">Línea de investigación</dt>
                                <dd class="col-sm-9">
                                    @if($thematicArea->investigationLine)
                                        <a href="{{ route('investigation-lines.show', $thematicArea->investigationLine) }}" class="text-decoration-none">
                                            {{ $thematicArea->investigationLine->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin línea</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Grupo de investigación</dt>
                                <dd class="col-sm-9">
                                    @if($thematicArea->investigationLine && $thematicArea->investigationLine->researchGroup)
                                        <a href="{{ route('research-groups.show', $thematicArea->investigationLine->researchGroup) }}" class="text-decoration-none">
                                            {{ $thematicArea->investigationLine->researchGroup->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin grupo</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Creado</dt>
                                <dd class="col-sm-9">{{ $thematicArea->created_at?->format('d/m/Y H:i') ?? 'N/D' }}</dd>

                                <dt class="col-sm-3">Última actualización</dt>
                                <dd class="col-sm-9">{{ $thematicArea->updated_at?->diffForHumans() ?? 'N/D' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
