{{--
    View path: framework/index.blade.php.
    Purpose: Presents the Tablar-styled listing for academic frameworks with filters, actions and pagination.
    Expected variables within this template: $frameworks, $search, $year, $perPage, $perPageOptions.
    Included partials or components: tablar::common.alert.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Str;
    $search = $search ?? null;
    $year = $year ?? null;
    $perPage = $perPage ?? 10;
    $perPageOptions = $perPageOptions ?? [10, 20, 30];
@endphp

@section('title', 'Gestión de Marcos')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb keeps the navigation context consistent with other catalog pages. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Marcos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <line x1="9" y1="9" x2="15" y2="9" />
                            <line x1="9" y1="15" x2="15" y2="15" />
                        </svg>
                        Gestión de Marcos
                        <span class="badge bg-azure ms-2">{{ $frameworks->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra los marcos curriculares y mantén su vigencia actualizada.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('frameworks.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo marco
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
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    {{-- Form captures the filtering criteria for the frameworks table. --}}
                    <form method="GET" action="{{ route('frameworks.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-xl-4">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="10" cy="10" r="7" />
                                        <line x1="21" y1="21" x2="15" y2="15" />
                                    </svg>
                                </span>
                                <input type="search" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Nombre, descripción o ID">
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl-2">
                            <label for="year" class="form-label">Año</label>
                            <input type="number" id="year" name="year" value="{{ $year }}" class="form-control" min="1900" max="{{ date('Y') + 50 }}" placeholder="Vigencia">
                        </div>
                        <div class="col-6 col-md-3 col-xl-2">
                            <label for="per_page" class="form-label">Registros</label>
                            <select id="per_page" name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" {{ (int) $perPage === (int) $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 5h16" />
                                    <path d="M7 10h10" />
                                    <path d="M10 15h4" />
                                </svg>
                                Aplicar Filtros
                            </button>
                        </div>
                        <div class="col-12 col-md-6 col-xl-2">
                            <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 220px;">Nombre</th>
                                <th class="text-truncate" style="max-width: 260px;">Descripción</th>
                                <th class="text-truncate" style="max-width: 200px;">Vigencia</th>
                                <th class="text-truncate" style="max-width: 160px;">Estado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($frameworks as $index => $framework)
                                @php
                                    $rowNumber = $frameworks->firstItem() ? $frameworks->firstItem() + $index : $index + 1;
                                    $startYear = (int) $framework->start_year;
                                    $endYear = $framework->end_year ? (int) $framework->end_year : null;
                                    $currentYear = (int) date('Y');
                                    $isCurrent = $startYear <= $currentYear && ($endYear === null || $endYear >= $currentYear);
                                    $isFuture = $startYear > $currentYear;
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $rowNumber }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $framework->name }}">{{ $framework->name }}</span>
                                            @if(!empty($framework->link))
                                                <a href="{{ $framework->link }}" target="_blank" rel="noopener noreferrer" class="small text-secondary text-truncate" style="max-width: 220px;">{{ $framework->link }}</a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        <span class="d-inline-block text-truncate" style="max-width: 260px;" title="{{ $framework->description }}">
                                            {{ Str::limit($framework->description, 120) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                            {{ $framework->start_year }} – {{ $framework->end_year ? $framework->end_year : 'Indefinido' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($isCurrent)
                                            <span class="badge bg-success-lt">Vigente</span>
                                        @elseif($isFuture)
                                            <span class="badge bg-info-lt">Programado</span>
                                        @else
                                            <span class="badge bg-yellow-lt">Finalizado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('frameworks.show', $framework) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="2" />
                                                    <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('frameworks.edit', $framework) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#framework-delete-modal" data-framework-name="{{ $framework->name }}" data-destroy-url="{{ route('frameworks.destroy', $framework) }}" title="Eliminar">
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
                                    <td colspan="6">
                                        <div class="empty">
                                            <div class="empty-img">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="14" rx="2" />
                                                    <path d="M8 21h8" />
                                                    <path d="M10 17h4" />
                                                </svg>
                                            </div>
                                            <p class="empty-title">No hay frameworks registrados</p>
                                            <p class="empty-subtitle text-secondary">Crea un nuevo framework para comenzar a organizar la oferta académica.</p>
                                            <div class="empty-action">
                                                <a href="{{ route('frameworks.create') }}" class="btn btn-primary">
                                                    Registrar framework
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($frameworks->hasPages())
                    <div class="card-footer">
                        {{ $frameworks->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal handles delete confirmations without relying on the native browser dialog. --}}
    <div class="modal fade" id="framework-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar framework</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="framework-delete-message">¿Deseas eliminar este framework?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="framework-delete-form" method="POST">
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
            const modalElement = document.getElementById('framework-delete-modal');
            const messageElement = document.getElementById('framework-delete-message');
            const formElement = document.getElementById('framework-delete-form');

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const frameworkName = trigger.getAttribute('data-framework-name') ?? 'este framework';
                const destroyUrl = trigger.getAttribute('data-destroy-url');

                if (messageElement) {
                    messageElement.textContent = `¿Deseas eliminar "${frameworkName}"? Esta acción no se puede deshacer.`;
                }

                if (formElement && destroyUrl) {
                    formElement.setAttribute('action', destroyUrl);
                }
            });
        });
    </script>
@endpush
