{{--
    View path: city-program/index.blade.php.
    Purpose: Lists the city-program relationships using the Tablar layout.
    Expected variables: $cityPrograms, $perPage.
    Included partials: tablar::common.alert.
--}}
@extends('tablar::page')

@section('title', 'Relaciones ciudad - programa')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb mirrors the structure used in the versions module. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ciudad - Programa</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 11l3 3l8 -8" />
                            <path d="M4 7h4l2 9l4 -4h5" />
                        </svg>
                        Asignación de programas por ciudad
                        <span class="badge bg-indigo ms-2">{{ $cityPrograms->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra qué programas académicos se imparten en cada ciudad.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('city-program.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Nueva relación
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Opciones de listado</h3>
                </div>
                <div class="card-body">
                    {{-- Small form keeps the interface aligned with the versions module. --}}
                    <form method="GET" action="{{ route('city-program.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <label for="per_page" class="form-label">Registros por página</label>
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                                Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Relaciones registradas</h3>
                    <div class="card-actions">
                        <span class="badge bg-indigo-lt">{{ $cityPrograms->total() }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    {{-- Table mirrors the visual language of the versions module. --}}
                    <table class="table card-table table-vcenter align-middle">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th class="text-truncate" style="max-width: 220px;">Ciudad</th>
                                <th class="text-truncate" style="max-width: 260px;">Programa</th>
                                <th class="w-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($cityPrograms as $assignment)
                            <tr>
                                <td class="text-muted">{{ $assignment->id }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $assignment->city->name ?? 'Sin ciudad' }}">
                                        {{ $assignment->city->name ?? 'Sin ciudad' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 260px;" title="{{ $assignment->program->name ?? 'Sin programa' }}">
                                        {{ $assignment->program->name ?? 'Sin programa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        <a href="{{ route('city-program.show', $assignment) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('city-program.edit', $assignment) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('city-program.destroy', $assignment) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta relación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="4" y1="7" x2="20" y2="7" />
                                                    <line x1="10" y1="11" x2="10" y2="17" />
                                                    <line x1="14" y1="11" x2="14" y2="17" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3h6v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No se encontraron relaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cityPrograms->hasPages())
                    <div class="card-footer">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                            <p class="m-0 text-muted">Mostrando {{ $cityPrograms->firstItem() }}-{{ $cityPrograms->lastItem() }} de {{ $cityPrograms->total() }} registros</p>
                            {{ $cityPrograms->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
