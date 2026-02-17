{{--
    View path: city/index.blade.php.
    Purpose: Renders the index.blade view for the City module.
    Expected variables within this template: $cities, $city, $departmentId, $departmentName, $departments, $id, $index, $perPage, $search, $size.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Gestión de ciudades')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb communicates how the user arrived at this listing. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- First crumb represents the application landing area. --}}
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- Second crumb indicates that the current resource is "Ciudades". --}}
                            <li class="breadcrumb-item active" aria-current="page">Ciudades</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-cyan" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21v-13l9-4l9 4v13" />
                            <path d="M13 13h4v8h-10v-6h6" />
                        </svg>
                        {{-- Title summarises the purpose of the listing. --}}
                        Gestión de ciudades
                        {{-- Badge highlights the total number of records to provide quick feedback. --}}
                        <span class="badge bg-cyan ms-2">{{ $cities->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra las ciudades disponibles y asócialas con su departamento correspondiente.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    {{-- Primary action button opens the creation form for a new city. --}}
                    <a href="{{ route('cities.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nueva ciudad
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
                    {{-- Card title describes that the controls below filter the listing. --}}
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form method="GET" action="{{ route('cities.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            {{-- Label describing the purpose of 'Buscar'. --}}
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                {{-- Input element used to capture the 'search' value. --}}
                                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre de la ciudad">
                                @if(!empty($search) || !empty($departmentId) || ($perPage ?? 10) != 10)
                                    <a href="{{ route('cities.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Label describing the purpose of 'Departamento'. --}}
                            <label for="department_id" class="form-label">Departamento</label>
                            {{-- Dropdown presenting the available options for 'department_id'. --}}
                            <select name="department_id" id="department_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($departments as $id => $departmentName)
                                    <option value="{{ $id }}" {{ (string)($departmentId ?? '') === (string)$id ? 'selected' : '' }}>{{ $departmentName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            {{-- Label describing the purpose of 'Registros por página'. --}}
                            <label for="per_page" class="form-label">Registros por página</label>
                            {{-- Dropdown presenting the available options for 'per_page'. --}}
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            {{-- Button element of type 'submit' to trigger the intended action. --}}
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
                    {{-- Table presents the filtered results in a structured format. --}}
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 220px;">Ciudad</th>
                                <th class="text-truncate" style="max-width: 220px;">Departamento</th>
                                <th>Creado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($cities as $index => $city)
                            <tr>
                                <td class="text-muted">{{ $cities->firstItem() + $index }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $city->name }}">{{ $city->name }}</span>
                                </td>
                                <td>
                                    @if($city->department)
                                        <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $city->department->name }}">{{ $city->department->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $city->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        {{-- Action button shows the full record details. --}}
                                        <a href="{{ route('cities.show', $city) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        {{-- Action button redirects to the edit screen for inline modifications. --}}
                                        <a href="{{ route('cities.edit', $city) }}" class="btn btn-sm btn-outline-success" title="Editar">
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
                                                data-destroy-url="{{ route('cities.destroy', $city) }}">
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
                                    No se encontraron ciudades con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cities->hasPages())
                    <div class="card-footer">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                            {{-- Pagination summary communicates the current slice being displayed. --}}
                            <p class="m-0 text-muted">Mostrando {{ $cities->firstItem() }}-{{ $cities->lastItem() }} de {{ $cities->total() }} resultados</p>
                            {{-- Pagination links allow navigation through additional pages of results. --}}
                            {{ $cities->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
        </div>
    </div>
</div>

{{-- Modal handles delete confirmations without relying on the native browser dialog. --}}
<div class="modal fade" id="city-delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar ciudad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="city-delete-message">¿Deseas eliminar esta ciudad?</p>
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

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('city-delete-modal');
            const messageElement = document.getElementById('city-delete-message');
            const formElement = document.getElementById('city-delete-form');

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const cityName = trigger.getAttribute('data-city-name') ?? 'esta ciudad';
                const destroyUrl = trigger.getAttribute('data-destroy-url');

                if (messageElement) {
                    messageElement.textContent = `¿Deseas eliminar la ciudad ${cityName}?`;
                }

                if (formElement && destroyUrl) {
                    formElement.setAttribute('action', destroyUrl);
                }
            });
        });
    </script>
@endpush
