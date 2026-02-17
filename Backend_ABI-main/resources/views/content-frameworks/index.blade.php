{{--
    View path: content-frameworks/index.blade.php.
    Purpose: Renders the index view for framework contents with Tablar styling.
    Expected variables within this template: $contentFrameworks, $frameworkOptions, $framework_id, $search.
    Included partials or components: tablar::common.alert.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Contenidos del marco')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb keeps the user aware of the current module. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contenidos de marco</li>
                        </ol>
                    </nav>

                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <line x1="9" y1="9" x2="15" y2="9" />
                            <line x1="9" y1="15" x2="15" y2="15" />
                        </svg>
                        Contenidos de marco
                        <span class="badge bg-primary ms-2">{{ $contentFrameworks->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Gestiona los contenidos que pertenecen a cada marco institucional.</p>
                </div>

                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('content-frameworks.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo contenido
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
                    <form method="GET" action="{{ route('content-frameworks.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre o descripción…">
                                @if(!empty($search) || !empty($framework_id))
                                    <a href="{{ route('content-frameworks.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="framework_id" class="form-label">Marco</label>
                            <select name="framework_id" id="framework_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($frameworkOptions ?? [] as $id => $name)
                                    <option value="{{ $id }}" {{ (string)($framework_id ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-3">
                            <button type="submit" class="btn btn-primary w-100">
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
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 200px;">Nombre</th>
                                <th class="text-truncate" style="max-width: 200px;">Marco</th>
                                <th class="text-truncate" style="max-width: 320px;">Descripción</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contentFrameworks as $index => $item)
                                <tr>
                                    <td class="text-muted">{{ $contentFrameworks->firstItem() + $index }}</td>
                                    <td class="text-truncate" style="max-width: 200px;" title="{{ $item->name }}">{{ $item->name }}</td>
                                    <td class="text-truncate" style="max-width: 200px;">
                                        @if($item->framework)
                                            <a href="{{ route('frameworks.show', $item->framework) }}" class="text-decoration-none" title="{{ $item->framework->name }}">
                                                {{ Str::limit($item->framework->name, 36) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Sin framework</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 320px;" title="{{ $item->description }}">
                                            {{ Str::limit($item->description, 90) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('content-frameworks.show', $item) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="2" />
                                                    <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('content-frameworks.edit', $item) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#content-framework-delete-modal" data-record-name="{{ $item->name }}" data-destroy-url="{{ route('content-frameworks.destroy', $item) }}" title="Eliminar">
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
                                    <td colspan="5">
                                        <div class="empty py-4">
                                            <div class="empty-img">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="3" width="18" height="14" rx="2" />
                                                    <path d="M8 21h8" />
                                                    <path d="M10 17h4" />
                                                </svg>
                                            </div>
                                            <p class="empty-title">No hay contenidos registrados</p>
                                            <p class="empty-subtitle text-secondary">Crea un nuevo contenido para comenzar a completar los frameworks.</p>
                                            <div class="empty-action">
                                                <a href="{{ route('content-frameworks.create') }}" class="btn btn-primary">Registrar contenido</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contentFrameworks->hasPages())
                    <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="text-secondary">
                            Mostrando
                            <strong>{{ $contentFrameworks->firstItem() }}</strong>
                            a
                            <strong>{{ $contentFrameworks->lastItem() }}</strong>
                            de
                            <strong>{{ $contentFrameworks->total() }}</strong>
                            contenidos
                        </div>
                        {{ $contentFrameworks->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal replaces the native confirmation when deleting a framework content record. --}}
    <div class="modal fade" id="content-framework-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar contenido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="content-framework-delete-message">¿Deseas eliminar este contenido?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="content-framework-delete-form" method="POST">
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
            const modalElement = document.getElementById('content-framework-delete-modal');
            const messageElement = document.getElementById('content-framework-delete-message');
            const formElement = document.getElementById('content-framework-delete-form');

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const recordName = trigger.getAttribute('data-record-name') ?? 'este contenido';
                const destroyUrl = trigger.getAttribute('data-destroy-url');

                if (messageElement) {
                    messageElement.textContent = `¿Deseas eliminar "${recordName}"? Esta acción no se puede deshacer.`;
                }

                if (formElement && destroyUrl) {
                    formElement.setAttribute('action', destroyUrl);
                }
            });
        });
    </script>
@endpush
