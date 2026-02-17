{{--
    View path: city/create.blade.php.
    Purpose: Renders the create.blade view for the City module.
    This template does not rely on dynamic variables.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Nueva ciudad')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb clarifies navigation context so the user knows the parent section. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- Dashboard crumb mirrors the structure used within the projects module. --}}
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- Link points back to the index allowing the user to abort the creation flow. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Ciudades</a></li>
                            {{-- Static crumb indicating the current "create" step in the hierarchy. --}}
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    {{-- Title summarises that the following form will capture a new city. --}}
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-cyan" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21v-13l9-4l9 4v13" />
                            <path d="M13 13h4v8h-10v-6h6" />
                        </svg>
                        Registrar ciudad
                    </h2>
                    {{-- Helper sentence provides additional guidance about the relationship with departments. --}}
                    <p class="text-muted mb-0">Crea una nueva ciudad y asóciala a un departamento existente.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    {{-- Secondary action mirrors the back button provided in the projects module. --}}
                    <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Datos de la ciudad</h3>
                            <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                        </div>
                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form action="{{ route('cities.store') }}" method="POST" novalidate autocomplete="off">
                                @csrf
                                {{-- Shared partial renders the reusable form inputs for both create and edit screens. --}}
                                @includeFirst(['city.form', 'cities.form'])

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('cities.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Guardar ciudad
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
