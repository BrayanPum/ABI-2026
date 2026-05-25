@extends('tablar::page')

@section('title', 'Gestionar Formatos')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Gestionar Formatos</h2>
                <p class="text-muted mb-0">Crea y administra los tipos de formatos del sistema</p>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('formatos.tipos.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    Nuevo Formato
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tipos de Formato</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Roles con acceso</th>
                            <th>Campos</th>
                            <th>Estado</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr>
                                <td>
                                    <span class="avatar avatar-sm bg-{{ $tipo->color }} text-white me-2">
                                        <i class="{{ $tipo->icono }}"></i>
                                    </span>
                                    {{ $tipo->nombre }}
                                </td>
                                <td>{{ $tipo->codigo ?? '—' }}</td>
                                <td>
                                    @foreach ($tipo->roles_acceso as $rol)
                                        <span class="badge bg-secondary me-1">{{ $rol }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $tipo->campos()->count() }}</td>
                                <td>
                                    @if ($tipo->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('formatos.tipos.show', $tipo) }}" class="btn btn-sm btn-primary">Ver</a>
                                        <a href="{{ route('formatos.tipos.edit', $tipo) }}" class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('formatos.tipos.destroy', $tipo) }}" method="POST"
                                              style="display:inline-block"
                                              onsubmit="return confirm('¿Eliminar este formato y todos sus registros?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay formatos creados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $tipos->links() }}
            </div>
        </div>

    </div>
</div>

@endsection
