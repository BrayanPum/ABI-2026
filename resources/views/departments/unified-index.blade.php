@extends('tablar::page')

@section('title', 'Departamentos y Ciudades')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item">Estructura Academica</li>
                            <li class="breadcrumb-item active" aria-current="page">Departamentos y Ciudades</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h6v6h-6z" />
                            <path d="M14 4h6v6h-6z" />
                            <path d="M4 14h6v6h-6z" />
                            <path d="M17 17h3v3h-3z" />
                        </svg>
                        Departamentos y Ciudades
                        <span class="badge bg-indigo ms-2">{{ $departments->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Gestiona departamentos y sus ciudades relacionadas desde una sola pantalla.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list departments-cities-header-actions">
                        <a href="{{ route('departments.create', ['redirect_to' => $currentPath], false) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Agregar departamento
                        </a>
                        @if($selectedDepartment)
                            <a href="{{ route('cities.create', ['department_id' => $selectedDepartment->id, 'redirect_to' => $currentPath], false) }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                Agregar ciudad
                            </a>
                        @else
                            <span class="btn btn-outline-secondary disabled" aria-disabled="true">Selecciona un departamento para agregar ciudades</span>
                        @endif
                    </div>
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
                    <h3 class="card-title">Departamentos</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('departments-cities.index') }}" class="row g-3 align-items-end">
                        @if($selectedDepartmentId)
                            <input type="hidden" name="selected_department_id" value="{{ $selectedDepartmentId }}">
                        @endif
                        @if($citySearch !== '')
                            <input type="hidden" name="city_search" value="{{ $citySearch }}">
                        @endif
                        <input type="hidden" name="cities_per_page" value="{{ $cityPerPage }}">

                        <div class="col-12 col-md-8">
                            <label for="department_search" class="form-label">Buscar departamento</label>
                            <input type="text" id="department_search" name="department_search" value="{{ $departmentSearch }}" class="form-control" placeholder="Nombre del departamento">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="departments_per_page" class="form-label">Registros por pagina</label>
                            <select name="departments_per_page" id="departments_per_page" class="form-select">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ $departmentPerPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="btn-list departments-cities-filter-actions">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 6h16" />
                                        <path d="M4 12h10" />
                                        <path d="M4 18h4" />
                                    </svg>
                                    Aplicar filtros
                                </button>
                                <a href="{{ route('departments-cities.index', array_filter([
                                    'selected_department_id' => $selectedDepartmentId,
                                    'city_search' => $citySearch !== '' ? $citySearch : null,
                                    'cities_per_page' => $cityPerPage !== 10 ? $cityPerPage : null,
                                ]), false) }}" class="btn btn-outline-secondary">
                                    Borrar filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 240px;">Departamento</th>
                                <th class="text-center">Ciudades registradas</th>
                                <th class="d-none d-md-table-cell">Creado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($departments as $index => $department)
                            @php
                                $isSelected = $selectedDepartmentId === $department->id;
                                $selectQuery = array_merge(request()->except('cities_page'), ['selected_department_id' => $department->id]);
                                $toggleQuery = $isSelected
                                    ? array_filter(request()->except(['selected_department_id', 'cities_page', 'city_search', 'cities_per_page']))
                                    : $selectQuery;
                            @endphp
                            <tr class="{{ $isSelected ? 'table-active' : '' }}">
                                <td class="text-muted">{{ $departments->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-block text-truncate" style="max-width: 240px;" title="{{ $department->name }}">{{ $department->name }}</span>
                                        @if($isSelected)
                                            <span class="badge bg-indigo-lt">Seleccionado</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-indigo-lt">{{ $department->cities_count }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $department->created_at?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap departments-cities-row-actions">
                                        <a href="{{ route('departments-cities.index', $toggleQuery, false) }}{{ $isSelected ? '' : '#cities-section' }}" class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-secondary' }}" title="{{ $isSelected ? 'Ocultar ciudades' : 'Ver ciudades' }}">
                                            {{ $isSelected ? 'Ocultar ciudades' : 'Ver ciudades' }}
                                        </a>
                                        <a href="{{ route('departments-cities.index', $selectQuery, false) }}#cities-section" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('departments.edit', ['department' => $department, 'redirect_to' => $currentPath], false) }}" class="btn btn-sm btn-outline-success" title="Editar">
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
                                                data-destroy-url="{{ route('departments.destroy', ['department' => $department, 'redirect_to' => $currentPath], false) }}">
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
                            <p class="m-0 text-muted">Mostrando {{ $departments->firstItem() }}-{{ $departments->lastItem() }} de {{ $departments->total() }} resultados</p>
                            {{ $departments->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="card" id="cities-section">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h3 class="card-title mb-1">Ciudades</h3>
                        @if($selectedDepartment)
                            <p class="text-muted mb-0">Departamento seleccionado: <strong>{{ $selectedDepartment->name }}</strong></p>
                        @else
                            <p class="text-muted mb-0">Selecciona un departamento en la tabla superior para gestionar sus ciudades.</p>
                        @endif
                    </div>
                    @if($selectedDepartment)
                        <a href="{{ route('cities.create', ['department_id' => $selectedDepartment->id, 'redirect_to' => $currentPath], false) }}" class="btn btn-primary departments-cities-mobile-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Agregar ciudad
                        </a>
                    @endif
                </div>

                @if($selectedDepartment && $cities)
                    <div class="card-body">
                        <form method="GET" action="{{ route('departments-cities.index') }}" class="row g-3 align-items-end">
                            @if($departmentSearch !== '')
                                <input type="hidden" name="department_search" value="{{ $departmentSearch }}">
                            @endif
                            <input type="hidden" name="departments_per_page" value="{{ $departmentPerPage }}">
                            <input type="hidden" name="selected_department_id" value="{{ $selectedDepartment->id }}">

                            <div class="col-12 col-md-8">
                                <label for="city_search" class="form-label">Buscar ciudad</label>
                                <input type="text" id="city_search" name="city_search" value="{{ $citySearch }}" class="form-control" placeholder="Nombre de la ciudad">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="cities_per_page" class="form-label">Registros por pagina</label>
                                <select name="cities_per_page" id="cities_per_page" class="form-select">
                                    @foreach([10, 25, 50] as $size)
                                        <option value="{{ $size }}" {{ $cityPerPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="btn-list departments-cities-filter-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 6h16" />
                                            <path d="M4 12h10" />
                                            <path d="M4 18h4" />
                                        </svg>
                                        Aplicar filtros
                                    </button>
                                    <a href="{{ route('departments-cities.index', array_filter([
                                        'department_search' => $departmentSearch !== '' ? $departmentSearch : null,
                                        'departments_per_page' => $departmentPerPage !== 10 ? $departmentPerPage : null,
                                        'selected_department_id' => $selectedDepartment->id,
                                    ]), false) }}#cities-section" class="btn btn-outline-secondary">
                                        Borrar filtros
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th class="w-1">#</th>
                                    <th class="text-truncate" style="max-width: 220px;">Ciudad</th>
                                    <th class="d-none d-md-table-cell text-truncate" style="max-width: 220px;">Departamento</th>
                                    <th class="d-none d-md-table-cell">Creado</th>
                                    <th class="w-1">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($cities as $index => $city)
                                <tr>
                                    <td class="text-muted">{{ $cities->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $city->name }}">{{ $city->name }}</span>
                                            <small class="text-muted d-md-none">{{ $city->department?->name ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $city->department?->name }}">{{ $city->department?->name ?? '-' }}</span>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $city->created_at?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap departments-cities-row-actions">
                                            <a href="{{ route('departments-cities.index', ['selected_department_id' => $city->department_id], false) }}#cities-section" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="2" />
                                                    <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('cities.edit', ['city' => $city, 'redirect_to' => $currentPath], false) }}" class="btn btn-sm btn-outline-success" title="Editar">
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
                                                    data-bs-target="#city-delete-modal"
                                                    data-city-name="{{ $city->name }}"
                                                    data-destroy-url="{{ route('cities.destroy', ['city' => $city, 'redirect_to' => $currentPath], false) }}">
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
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No se encontraron ciudades para el departamento seleccionado con los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($cities->hasPages())
                        <div class="card-footer">
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                                <p class="m-0 text-muted">Mostrando {{ $cities->firstItem() }}-{{ $cities->lastItem() }} de {{ $cities->total() }} resultados</p>
                                {{ $cities->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="card-body text-muted">
                        Selecciona un departamento desde la tabla superior para visualizar y administrar sus ciudades asociadas en esta misma pantalla.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="department-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="department-delete-message">Deseas eliminar este departamento?</p>
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

    <div class="modal fade" id="city-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar ciudad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="city-delete-message">Deseas eliminar esta ciudad?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="city-delete-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        @media (max-width: 767.98px) {
            .departments-cities-header-actions,
            .departments-cities-filter-actions,
            .departments-cities-row-actions {
                width: 100%;
            }

            .departments-cities-header-actions .btn,
            .departments-cities-header-actions .disabled,
            .departments-cities-filter-actions .btn,
            .departments-cities-row-actions .btn,
            .departments-cities-mobile-full {
                width: 100%;
                justify-content: center;
            }

            .departments-cities-row-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: .5rem;
            }

            .table .btn-list.flex-nowrap {
                flex-wrap: wrap !important;
            }

            .table .text-truncate {
                max-width: 150px !important;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bindDeleteModal = ({ modalId, messageId, formId, dataNameKey, entityLabel }) => {
                const modalElement = document.getElementById(modalId);
                const messageElement = document.getElementById(messageId);
                const formElement = document.getElementById(formId);

                modalElement?.addEventListener('show.bs.modal', event => {
                    const trigger = event.relatedTarget;
                    if (!trigger) {
                        return;
                    }

                    const itemName = trigger.getAttribute(dataNameKey) ?? entityLabel;
                    const destroyUrl = trigger.getAttribute('data-destroy-url');

                    if (messageElement) {
                        messageElement.textContent = `Deseas eliminar ${entityLabel} ${itemName}?`;
                    }

                    if (formElement && destroyUrl) {
                        formElement.setAttribute('action', destroyUrl);
                    }
                });
            };

            bindDeleteModal({
                modalId: 'department-delete-modal',
                messageId: 'department-delete-message',
                formId: 'department-delete-form',
                dataNameKey: 'data-department-name',
                entityLabel: 'el departamento',
            });

            bindDeleteModal({
                modalId: 'city-delete-modal',
                messageId: 'city-delete-message',
                formId: 'city-delete-form',
                dataNameKey: 'data-city-name',
                entityLabel: 'la ciudad',
            });
        });
    </script>
@endpush
