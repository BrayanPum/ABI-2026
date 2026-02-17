{{--
    View path: contents/create.blade.php.
    Purpose: Presents a Tablar-styled form to register a new content objective
    associated with a specific framework while reusing the shared form partial.
--}}
@extends('tablar::page')

@section('title', 'Nuevo contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Frameworks</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.show', $framework) }}">{{ $framework->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nuevo contenido</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Registrar nuevo objetivo
                    </h2>
                    <p class="text-muted mb-0">Crea un objetivo para el framework <strong>{{ $framework->name }}</strong>.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('frameworks.show', $framework) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg>
                        Volver al framework
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
                            <h3 class="card-title">Detalles del objetivo</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('frameworks.contents.store', $framework) }}">
                                @csrf
                                @include('contents.form', ['showRoleSelector' => false])
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('frameworks.show', $framework) }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar objetivo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
