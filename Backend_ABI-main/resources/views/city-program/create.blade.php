{{--
    View path: city-program/create.blade.php.
    Purpose: Renders the creation screen for city-program assignments using the shared form.
    Expected variables: $cityProgram, $cities, $programs.
--}}
@extends('tablar::page')

@section('title', 'Nueva relación ciudad - programa')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('city-program.index') }}">Ciudad - Programa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 20l-8 -4l8 -4l8 4l-8 4" />
                            <path d="M4 12l8 -4l8 4" />
                        </svg>
                        Nueva relación ciudad - programa
                    </h2>
                    <p class="text-muted mb-0">Selecciona la ciudad y el programa que se impartirá en ella.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('city-program.index') }}" class="btn btn-outline-secondary">
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

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos de la relación</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form posts the selected relationship to the controller. --}}
                            <form method="POST" action="{{ route('city-program.store') }}">
                                @csrf
                                @include('city-program.form')
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recomendaciones</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <span class="avatar bg-indigo-lt text-indigo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M17 11v8l3 3" />
                                            <path d="M7 21a4 4 0 0 1 8 0" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Verifica la ciudad</strong>
                                        <p class="text-secondary mb-0">Confirma que la ciudad exista en el catálogo antes de crear la relación.</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <span class="avatar bg-indigo-lt text-indigo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16v4h-16z" />
                                            <path d="M6 8v9a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2v-9" />
                                            <path d="M10 12h4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Evita duplicados</strong>
                                        <p class="text-secondary mb-0">Cada ciudad solo puede tener un registro por programa.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
