@extends('tablar::page')

@section('title', 'Módulo de Formatos')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Módulo de Formatos</h2>
                <p class="text-muted mb-0">Gestión de formatos institucionales para proyectos de grado.</p>
            </div>
            @if (auth()->user()->role === 'research_staff')
            <div class="col-auto ms-auto">
                <a href="{{ route('formatos.tipos.index') }}" class="btn btn-primary">
                    <i class="ti ti-settings me-1"></i> Gestionar Formatos
                </a>
                <a href="{{ route('formatos.tipos.create') }}" class="btn btn-success ms-2">
                    <i class="ti ti-plus me-1"></i> Nuevo Formato
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @if ($formatos->isEmpty())
            <div class="alert alert-info">No hay formatos disponibles para tu rol.</div>
        @else
            <div class="row row-cards">
                @foreach ($formatos as $formato)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="bg-{{ $formato->color }} text-white avatar me-3">
                                        <i class="{{ $formato->icono }} fs-2"></i>
                                    </span>
                                    <div>
                                        <h3 class="card-title mb-0">{{ $formato->nombre }}</h3>
                                        @if ($formato->codigo)
                                            <p class="text-muted small mb-0">{{ $formato->codigo }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted">{{ $formato->descripcion }}</p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('formatos.registros.index', $formato) }}"
                                   class="btn btn-{{ $formato->color }} w-100">
                                    <i class="ti ti-arrow-right me-1"></i> Ir al formato
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

@endsection
