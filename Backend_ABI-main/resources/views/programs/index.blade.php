{{--
    View path: programs/index.blade.php.
    Purpose: Renders the index view for the Programs module using the Tablar layout.
    Expected variables within this template: $index, $perPage, $program, $programs, $researchGroupId,
    $researchGroups, $search.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Programas académicos')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb clarifies the navigation path within the academic catalogue. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Programas académicos</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-teal" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 4h18" />
                            <path d="M5 8h14" />
                            <path d="M7 12h10" />
                            <path d="M9 16h6" />
                            <path d="M11 20h2" />
                        </svg>
                        Programas académicos
                        <span class="badge bg-teal ms-2">{{ $programs->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Gestiona los programas educativos y asócialos a sus grupos de investigación.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    {{-- Primary action button opens the creation flow for a new program. --}}
                    <a href="{{ route('programs.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo programa
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
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="10" cy="10" r="7" />
                            <line x1="21" y1="21" x2="15" y2="15" />
                        </svg>
                        Filtros de búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    {{-- Form captures the filters that shape the program listing. --}}
                    <form method="GET" action="{{ route('programs.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-lg-6 col-xl-5">
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                <input type="text" id="search" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre o código…">
                                @if(!empty($search) || !empty($researchGroupId) || ($perPage ?? 10) != 10)
                                    <a href="{{ route('programs.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-4">
                            <label for="research_group_id" class="form-label">Grupo de investigación</label>
                            <select name="research_group_id" id="research_group_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($researchGroups as $id => $groupName)
                                    <option value="{{ $id }}" {{ (string)($researchGroupId ?? '') === (string)$id ? 'selected' : '' }}>{{ $groupName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-xl-2">
                            <label for="per_page" class="form-label">Registros por página</label>
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([10, 25, 50] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-xl-2 d-grid">
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
                    <h3 class="card-title">Listado de programas</h3>
                    <div class="card-actions">
                        <span class="badge bg-teal-lt">{{ $programs->total() }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    {{-- Table lists each program along with its code and associated research group. --}}
                    <table class="table card-table table-vcenter align-middle">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th style="max-width: 140px;" class="text-truncate">Código</th>
                                <th style="max-width: 280px;" class="text-truncate">Programa</th>
                                <th style="max-width: 320px;" class="text-truncate">Grupo de investigación</th>
                                <th class="w-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($programs as $index => $program)
                            <tr>
                                <td class="text-muted">{{ $programs->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-teal-lt" title="Código del programa">{{ $program->code }}</span>
                                </td>
                                <td class="text-truncate" style="max-width: 280px;">
                                    <span class="d-inline-block text-truncate" style="max-width: 100%;" title="{{ $program->name }}">{{ $program->name }}</span>
                                </td>
                                <td class="text-truncate" style="max-width: 320px;">
                                    @if($program->researchGroup)
                                        <a href="{{ route('research-groups.show', $program->researchGroup) }}" class="text-decoration-none text-reset d-inline-block text-truncate" style="max-width: 100%;" title="{{ $program->researchGroup->name }}">
                                            {{ $program->researchGroup->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin grupo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        {{-- Action button lets administrators review the program details. --}}
                                        <a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        {{-- Action button opens the edit form to adjust this program. --}}
                                        <a href="{{ route('programs.edit', $program) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        {{-- Dedicated form is triggered via the custom confirmation modal. --}}
                                        <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-none" id="delete-program-{{ $program->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Eliminar"
                                                data-delete-form="delete-program-{{ $program->id }}"
                                                data-program-name="{{ $program->name }}">
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
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <rect x="3" y="4" width="18" height="12" rx="2" />
                                                <line x1="7" y1="20" x2="17" y2="20" />
                                                <line x1="9" y1="16" x2="9" y2="20" />
                                                <line x1="15" y1="16" x2="15" y2="20" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay programas registrados</p>
                                        <p class="empty-subtitle text-muted">Crea un programa y vincúlalo a su grupo de investigación correspondiente.</p>
                                        <div class="empty-action">
                                            <a href="{{ route('programs.create') }}" class="btn btn-primary">Registrar programa</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($programs->hasPages())
                    <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        @php
                            $from = $programs->firstItem() ?? 0;
                            $to = $programs->lastItem() ?? 0;
                        @endphp
                        <div class="text-muted small">Mostrando {{ $from }}-{{ $to }} de {{ $programs->total() }} registros</div>
                        <nav aria-label="Paginación de programas académicos">
                            {{ $programs->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

{{-- Modal replaces the native confirmation dialog when deleting a program. --}}
<div class="modal modal-blur fade" id="program-delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar programa académico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="program-delete-message">¿Deseas eliminar este programa? Esta acción es reversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="program-delete-confirm">Eliminar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('program-delete-modal');
            const modalInstance = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            const messageElement = document.getElementById('program-delete-message');
            const confirmButton = document.getElementById('program-delete-confirm');
            let targetFormId = null;

            document.addEventListener('click', event => {
                const trigger = event.target.closest('[data-delete-form]');
                if (!trigger) {
                    return;
                }

                event.preventDefault();
                targetFormId = trigger.getAttribute('data-delete-form');
                const programName = trigger.getAttribute('data-program-name');
                messageElement.textContent = programName
                    ? `¿Deseas eliminar el programa "${programName}"? Esta acción es reversible.`
                    : '¿Deseas eliminar este programa? Esta acción es reversible.';
                confirmButton.disabled = false;
                confirmButton.innerHTML = 'Eliminar';
                modalInstance?.show();
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                targetFormId = null;
                confirmButton.disabled = false;
                confirmButton.innerHTML = 'Eliminar';
            });

            confirmButton.addEventListener('click', () => {
                if (!targetFormId) {
                    modalInstance?.hide();
                    return;
                }

                const form = document.getElementById(targetFormId);
                if (!form) {
                    modalInstance?.hide();
                    return;
                }

                confirmButton.disabled = true;
                confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';
                form.submit();
            });
        });
    </script>
@endpush
