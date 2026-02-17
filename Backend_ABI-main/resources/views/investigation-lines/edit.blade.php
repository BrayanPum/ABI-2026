{{--
    View path: investigation-lines/edit.blade.php.
    Purpose: Renders the edit.blade view for the Investigation Lines module.
    Expected variables within this template: $investigationLine.
    Included partials or components: investigation-lines.form, tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Editar línea de investigación')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('investigation-lines.index') }}">Líneas de investigación</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-purple" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16" />
                            <path d="M4 12h10" />
                            <path d="M4 18h4" />
                        </svg>
                        Editar línea: {{ $investigationLine->name }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('investigation-lines.show', $investigationLine) }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 7h14" />
                            <path d="M5 12h14" />
                            <path d="M5 17h14" />
                        </svg>
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar','display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Actualiza los datos necesarios y guarda los cambios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form method="POST" action="{{ route('investigation-lines.update', $investigationLine) }}">
                                @csrf
                                @method('PUT')
                                @include('investigation-lines.form')
                                <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-3">
                                    <a href="{{ route('investigation-lines.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Actualizar línea</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
