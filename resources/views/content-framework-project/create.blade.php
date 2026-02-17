{{--
    View path: content-framework-project/create.blade.php.
    Purpose: Presents the screen to create a new link between a project and a content framework entry.
--}}
@extends('tablar::page')

@section('title', 'Asignar contenido a proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-project.index') }}">Asignaciones de contenidos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Asignar contenido a proyecto
                    </h2>
                    <p class="text-muted mb-0">Selecciona un proyecto y un contenido para relacionarlos.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('content-framework-project.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos de la asignación</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('content-framework-project.store') }}" novalidate>
                                @csrf
                                @include('content-framework-project.form', [
                                    'contentFrameworkProject' => $contentFrameworkProject,
                                    'projects' => $projects,
                                    'contentFrameworks' => $contentFrameworks,
                                    'projectId' => $projectId,
                                    'contentFrameworkId' => $contentFrameworkId,
                                ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
