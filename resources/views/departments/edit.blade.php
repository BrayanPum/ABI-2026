{{-- View responsible for editing an existing department record. --}}
@extends('tablar::page')

@section('title', 'Editar departamento')

@section('content')
    {{-- Header with breadcrumb links to provide navigation context for the edit page. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departamentos</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('departments.show', $department) }}">{{ $department->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17h3v3h-3z" />
                        </svg>
                        Editar departamento
                    </h2>
                    <p class="text-muted mb-0">Actualiza la información del departamento seleccionado.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-secondary">Ver detalle</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Card displaying the department form pre-filled with the current data. --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Datos del departamento</h3>
                            <span class="badge bg-success-lt">ID {{ $department->id }}</span>
                        </div>
                        <div class="card-body">
                            {{--
                                Submit the updated department attributes using the PUT method so the
                                controller can persist the changes.
                            --}}
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form action="{{ route('departments.update', $department) }}" method="POST" novalidate autocomplete="off">
                                @csrf
                                @method('PUT')
                                {{-- Reuse the shared form partial, which adapts to edit mode automatically. --}}
                                @include('departments.form')

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('departments.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                        Guardar cambios
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
