@extends('tablar::page')

@section('title', $tipo->nombre)

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">{{ $tipo->nombre }}</h2>
                <p class="text-muted mb-0">{{ $tipo->descripcion }}</p>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('formatos.registros.create', $tipo) }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Nuevo Registro
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
                <h3 class="card-title">Registros</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Enviado por</th>
                            <th>Fecha</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $registro)
                            <tr>
                                <td>{{ $registro->id }}</td>
                                <td>{{ $registro->user?->name ?? '—' }}</td>
                                <td>{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('formatos.registros.show', [$tipo, $registro]) }}"
                                           class="btn btn-sm btn-primary">Ver</a>
                                        <a href="{{ route('formatos.registros.edit', [$tipo, $registro]) }}"
                                           class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('formatos.registros.destroy', [$tipo, $registro]) }}"
                                              method="POST" style="display:inline-block"
                                              onsubmit="return confirm('¿Eliminar este registro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay registros aún</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $registros->links() }}
            </div>
        </div>

    </div>
</div>

@endsection
