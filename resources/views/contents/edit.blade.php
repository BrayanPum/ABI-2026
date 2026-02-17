{{--
    View path: contents/edit.blade.php.
    Purpose: Provides a Tablar-styled form to update an existing content record
    while leveraging the shared form partial for consistency.
--}}
@extends('tablar::page')

@php
    use App\Models\Content;
    $roleLabels = [
        'research_staff' => 'Equipo de investigación',
        'professor' => 'Profesor',
        'student' => 'Estudiante',
        'committee_leader' => 'Líder de comité',
    ];
    $selectedRoles = old('roles', $content->roles ?? []);
    if (! is_array($selectedRoles)) {
        $selectedRoles = array_filter([$selectedRoles]);
    }
@endphp

@section('title', 'Editar contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('catalog.contents') }}">Contenidos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar contenido</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 20l4 -16" />
                            <path d="M8 20l4 -16" />
                            <path d="M4 20l4 -16" />
                            <path d="M16 20l4 -16" />
                        </svg>
                        Actualizar contenido #{{ $content->id }}
                    </h2>
                    <p class="text-muted mb-0">Modifica los datos del contenido y define qué roles pueden gestionarlo.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('catalog.contents') }}" class="btn btn-outline-secondary">
                        Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Actualiza los campos necesarios y guarda los cambios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('contents.update', $content) }}">
                                @csrf
                                @method('PUT')
                                @include('contents.form', ['content' => $content, 'selectedRoles' => $selectedRoles ?? []])
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('catalog.contents') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
