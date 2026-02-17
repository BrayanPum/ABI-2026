{{-- Listing page that manages departments, supports filtering, pagination, and CRUD actions. --}}
@extends('tablar::page')

@section('title', 'Gestión de departamentos')

@section('content')
    {{-- Header summarizing the page purpose and exposing navigation breadcrumbs. --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Departamentos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17h3v3h-3z" />
                        </svg>
                        Gestión de departamentos
                        <span class="badge bg-indigo ms-2">{{ $departments->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra los departamentos disponibles y organiza sus ciudades asociadas.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    {{-- Button that leads to the department creation flow. --}}
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo departamento
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                {{-- Global alert component displayed when Tablar configuration enables it. --}}
                @include('tablar::common.alert')
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    {{-- Filter form allows searching by name and adjusting pagination size. --}}
                    <form method="GET" action="{{ route('departments.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            {{-- Label describing the purpose of 'Buscar'. --}}
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                {{-- Input element used to capture the 'search' value. --}}
                                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre del departamento">
                                @if(!empty($search) || ($perPage ?? 10) != 10)
                                    {{-- Quick reset link appears when filters are active. --}}
                                    <a href="{{ route('departments.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Label describing the purpose of 'Registros por página'. --}}
                            <label for="per_page" class="form-label">Registros por página</label>
                            {{-- Selecting a new page size auto-submits to refresh the listing. --}}
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            {{-- Submit button triggers the GET request with the chosen filters. --}}
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16" />
                                    <path d="M4 12h10" />
                                    <path d="M4 18h4" />
                                </svg>
                                Aplicar filtros
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    {{-- Table presenting department metadata along with action buttons. --}}
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 240px;">Departamento</th>
                                <th class="text-center">Ciudades registradas</th>
                                <th>Creado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($departments as $index => $department)
                            {{-- Each row represents a department with counts and timestamps. --}}
                            <tr>
                                <td class="text-muted">{{ $departments->firstItem() + $index }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 240px;" title="{{ $department->name }}">{{ $department->name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-indigo-lt">{{ $department->cities_count }}</span>
                                </td>
                                <td>{{ $department->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    {{-- Action buttons for viewing, editing, or deleting the department. --}}
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Eliminar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#department-delete-modal"
                                                data-department-name="{{ $department->name }}"
                                                data-destroy-url="{{ route('departments.destroy', $department) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="4" y1="7" x2="20" y2="7" />
                                                <line x1="10" y1="11" x2="10" y2="17" />
                                                <line x1="14" y1="11" x2="14" y2="17" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3h6v3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty state when no departments satisfy the current filters. --}}
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No se encontraron departamentos con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($departments->hasPages())
                    <div class="card-footer">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                            {{-- Pagination summary and controls rendered when multiple pages exist. --}}
                            <p class="m-0 text-muted">Mostrando {{ $departments->firstItem() }}-{{ $departments->lastItem() }} de {{ $departments->total() }} resultados</p>
                            {{ $departments->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

{{-- Modal replaces the default browser confirm dialog when deleting a department. --}}
<div class="modal fade" id="department-delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar departamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="department-delete-message">¿Deseas eliminar este departamento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="department-delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>



@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('department-delete-modal');
            const messageElement = document.getElementById('department-delete-message');
            const formElement = document.getElementById('department-delete-form');

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const departmentName = trigger.getAttribute('data-department-name') ?? 'este departamento';
                const destroyUrl = trigger.getAttribute('data-destroy-url');

                if (messageElement) {
                    messageElement.textContent = `¿Deseas eliminar el departamento ${departmentName}?`;
                }

                if (formElement && destroyUrl) {
                    formElement.setAttribute('action', destroyUrl);
                }
            });
        });
    </script>
@endpush
