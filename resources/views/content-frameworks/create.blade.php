{{--
    View path: content-frameworks/create.blade.php.
    Purpose: Presents the create screen for framework contents using the shared form fragment.
--}}
@extends('tablar::page')

@section('title', 'Registrar contenido del marco')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-frameworks.index') }}">Contenidos del marco</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Registrar contenido del marco
                    </h2>
                    <p class="text-muted mb-0">Captura la información del contenido y relaciónala con el marco correspondiente.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('content-frameworks.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
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
                            <h3 class="card-title">Información del contenido</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('content-frameworks.store') }}" novalidate>
                                @csrf
                                @php($prefw = $prefw ?? request('framework_id'))
                                @include('content-frameworks.form', [
                                    'contentFramework' => $contentFramework ?? null,
                                    'frameworks' => $frameworks ?? null,
                                    'prefw' => $prefw,
                                ])
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
