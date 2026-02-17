{{-- View responsible for rendering the creation form for a new department. --}}
@extends('tablar::page')

@section('title', 'Nuevo departamento')

@section('content')
    {{-- Page header with breadcrumb navigation for contextual awareness. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departamentos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17h3v3h-3z" />
                        </svg>
                        Registrar departamento
                    </h2>
                    <p class="text-muted mb-0">Crea un nuevo departamento para asociar ciudades y programas.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content container holding the department registration form. --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Datos del departamento</h3>
                            <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                        </div>
                        <div class="card-body">
                            {{-- Form submits the department information to the store route. --}}
                            <form action="{{ route('departments.store') }}" method="POST" novalidate autocomplete="off">
                                @csrf
                                {{-- Reuse the shared form partial for both create and edit flows. --}}
                                @include('departments.form')

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('departments.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Guardar departamento
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
