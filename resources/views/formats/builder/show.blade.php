@extends('tablar::page')

@section('title', $tipo->nombre)

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.tipos.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">{{ $tipo->nombre }}</h2>
                <p class="text-muted mb-0">{{ $tipo->codigo }}</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('formatos.registros.index', $tipo) }}" class="btn btn-success me-2">
                    <i class="ti ti-list me-1"></i> Ver Registros
                </a>
                <a href="{{ route('formatos.tipos.edit', $tipo) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nombre</dt>
                    <dd class="col-sm-9">{{ $tipo->nombre }}</dd>

                    <dt class="col-sm-3">Código</dt>
                    <dd class="col-sm-9">{{ $tipo->codigo ?? '—' }}</dd>

                    <dt class="col-sm-3">Descripción</dt>
                    <dd class="col-sm-9">{{ $tipo->descripcion ?? '—' }}</dd>

                    <dt class="col-sm-3">Roles con acceso</dt>
                    <dd class="col-sm-9">
                        @foreach ($tipo->roles_acceso as $rol)
                            <span class="badge bg-secondary me-1">{{ $rol }}</span>
                        @endforeach
                    </dd>

                    <dt class="col-sm-3">Estado</dt>
                    <dd class="col-sm-9">
                        @if ($tipo->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Total registros</dt>
                    <dd class="col-sm-9">{{ $tipo->registros()->count() }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Campos del Formulario ({{ $tipo->campos->count() }})</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Etiqueta</th>
                            <th>Tipo</th>
                            <th>Sección</th>
                            <th class="text-center">Requerido</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tipo->campos as $campo)
                            <tr>
                                <td>{{ $campo->orden + 1 }}</td>
                                <td>{{ $campo->etiqueta }}</td>
                                <td><span class="badge bg-blue-lt">{{ $campo->tipo }}</span></td>
                                <td>{{ $campo->seccion ?? '—' }}</td>
                                <td class="text-center">
                                    @if ($campo->requerido)
                                        <span class="badge bg-danger">Sí</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($campo->opciones)
                                        {{ collect($campo->opciones)->pluck('etiqueta')->implode(', ') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
