{{--
    View path: city-program/edit.blade.php.
    Purpose: Renders the edit screen for city-program assignments using the shared form.
    Expected variables: $cityProgram, $cities, $programs.
--}}
@extends('tablar::page')

@section('title', 'Editar relación ciudad - programa')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('city-program.index') }}">Ciudad - Programa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar relación #{{ $cityProgram->id }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 20h4l10 -10a2.828 2.828 0 1 0 -4 -4l-10 10v4" />
                            <path d="M13.5 6.5l4 4" />
                        </svg>
                        Editar relación ciudad - programa
                    </h2>
                    <p class="text-muted mb-0">Actualiza la ciudad o el programa asociados a este registro.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('city-program.show', $cityProgram) }}" class="btn btn-outline-secondary">
                        Ver detalle
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
                                <span class="badge bg-indigo-lt">ID {{ $cityProgram->id }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form sends the updated data to the controller. --}}
                            <form method="POST" action="{{ route('city-program.update', $cityProgram) }}">
                                @csrf
                                @method('PUT')
                                @include('city-program.form')
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Consejos rápidos</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <span class="avatar bg-indigo-lt text-indigo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20l-8 -4l8 -4l8 4l-8 4" />
                                            <path d="M4 12l8 -4l8 4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Consulta los catálogos</strong>
                                        <p class="text-secondary mb-0">Si necesitas una ciudad o programa nuevo, créalos antes de editar este registro.</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <span class="avatar bg-indigo-lt text-indigo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 8v8" />
                                            <path d="M8 12h8" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Mantén la unicidad</strong>
                                        <p class="text-secondary mb-0">No dupliques combinaciones ciudad-programa, aprovecha la validación existente.</p>
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
