{{--
    View path: city/show.blade.php.
    Purpose: Renders the show.blade view for the City module.
    Expected variables within this template: $city.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Detalle de la ciudad')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb highlights the parent list and current record. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- Navigational link returns to the list of cities. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Ciudades</a></li>
                            {{-- Active crumb displays the selected city's name. --}}
                            <li class="breadcrumb-item active" aria-current="page">{{ $city->name }}</li>
                        </ol>
                    </nav>
                    {{-- Title mirrors the record name for clarity. --}}
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-cyan" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21v-13l9-4l9 4v13" />
                            <path d="M13 13h4v8h-10v-6h6" />
                        </svg>
                        {{ $city->name }}
                    </h2>
                    {{-- Helper text explains what details the user will find below. --}}
                    <p class="text-muted mb-0">Información detallada de la ciudad y su departamento asociado.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Action button opens the edit form for this city. --}}
                        <a href="{{ route('cities.edit', $city) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar
                        </a>
                        {{-- Secondary action returns to the table without making changes. --}}
                        <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary">
                            Volver al listado
                        </a>
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
                            {{-- Header explains that the following list covers the core attributes. --}}
                            <h3 class="card-title mb-0">Información general</h3>
                            <span class="badge bg-cyan-lt">ID {{ $city->id }}</span>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                {{-- Display pair summarises the stored city name. --}}
                                <dt class="col-sm-5 text-muted">Nombre</dt>
                                <dd class="col-sm-7">{{ $city->name }}</dd>

                                {{-- Display pair reveals the linked department, if any. --}}
                                <dt class="col-sm-5 text-muted">Departamento</dt>
                                <dd class="col-sm-7">
                                    @if($city->department)
                                        {{-- Link allows quick navigation to the related department profile. --}}
                                        <a href="{{ route('departments.show', $city->department) }}">{{ $city->department->name }}</a>
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </dd>
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
                                {{-- Timestamps help administrators audit record creation and updates. --}}
                                <dt class="col-sm-5 text-muted">Creado</dt>
                                <dd class="col-sm-7">{{ $city->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Última actualización</dt>
                                <dd class="col-sm-7">{{ $city->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </dl>

                            @if($city->department)
                                {{-- Quick navigation for administrators who need to manage the related department. --}}
                                <div class="mt-4">
                                    <a href="{{ route('departments.show', $city->department) }}" class="btn btn-sm btn-outline-primary">
                                        Ver departamento asociado
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
