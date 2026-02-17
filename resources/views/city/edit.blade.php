{{--
    View path: city/edit.blade.php.
    Purpose: Renders the edit.blade view for the City module.
    Expected variables within this template: $city.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Editar ciudad')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb trail confirms the current item and step in the editing flow. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- First crumb returns to the general listing if the user cancels. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Ciudades</a></li>
                            {{-- Second crumb links to the detail page for quick verification before editing. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.show', $city) }}">{{ $city->name }}</a></li>
                            {{-- Final crumb indicates the current edit action. --}}
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    {{-- Title communicates that existing information will be updated. --}}
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                        </svg>
                        Editar ciudad
                    </h2>
                    {{-- Contextual helper assures the user about the scope of the update. --}}
                    <p class="text-muted mb-0">Actualiza los datos de la ciudad seleccionada.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    {{-- Quick access back to the detailed record or listing. --}}
                    <a href="{{ route('cities.show', $city) }}" class="btn btn-outline-secondary">Ver detalle</a>
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
                            {{-- Heading groups the editable attributes of the city. --}}
                            <h3 class="card-title mb-0">Datos de la ciudad</h3>
                            <span class="badge bg-success-lt">ID {{ $city->id }}</span>
                        </div>
                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form action="{{ route('cities.update', $city) }}" method="POST" novalidate autocomplete="off">
                                @csrf
                                @method('PUT')
                                {{-- Partial reused from the create screen keeps the form consistent across flows. --}}
                                @includeFirst(['city.form', 'cities.form'])

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('cities.index') }}" class="btn btn-link">Cancelar</a>
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
