{{--
    View path: users/index.blade.php.
    Purpose: Renders the index view for the Users module following the same layout conventions
    as the projects catalogue.
    Expected variables within this template: $cityProgramId, $cityPrograms, $perPage, $perPageOptions,
    $role, $search, $state, $users.
    Included partials or components: tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Usuarios')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb clarifies the user's current location within the admin panel. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- Link allows returning to the dashboard overview. --}}
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            {{-- Active crumb highlights the users module. --}}
                            <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        {{-- Title summarises that the table below lists system users. --}}
                        Usuarios
                        {{-- Badge exposes the total record count given the current filter combination. --}}
                        <span class="badge bg-indigo ms-2">{{ $users->total() }}</span>
                    </h2>
                    <p class="text-muted mb-0">Administra los usuarios del sistema según su rol y estado.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    {{-- Primary action button creates new accounts directly from the index. --}}
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Nuevo usuario
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
                    <h3 class="card-title">Filtros de búsqueda</h3>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-xl-4">
                            {{-- Label describing the purpose of 'Buscar'. --}}
                            <label for="search" class="form-label">Buscar</label>
                            <div class="input-group">
                                {{-- Input element used to capture the 'search' value. --}}
                                <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Email, nombre, cédula…">
                                @if(!empty($search) || !empty($role) || !empty($state) || !empty($cityProgramId) || ($perPage ?? 10) != 10)
                                    <a href="{{ route('users.index') }}" class="input-group-text" title="Limpiar filtros">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-2">
                            {{-- Label describing the purpose of 'Rol'. --}}
                            <label for="role" class="form-label">Rol</label>
                            {{-- Dropdown presenting the available options for 'role'. --}}
                            <select name="role" id="role" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <option value="student" {{ (string)($role ?? '') === 'student' ? 'selected' : '' }}>Estudiante</option>
                                <option value="professor" {{ (string)($role ?? '') === 'professor' ? 'selected' : '' }}>Profesor</option>
                                <option value="committee_leader" {{ (string)($role ?? '') === 'committee_leader' ? 'selected' : '' }}>Líder de Comité</option>
                                <option value="research_staff" {{ (string)($role ?? '') === 'research_staff' ? 'selected' : '' }}>Personal de Investigación</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-2">
                            {{-- Label describing the purpose of 'Estado'. --}}
                            <label for="state" class="form-label">Estado</label>
                            {{-- Dropdown presenting the available options for 'state'. --}}
                            <select name="state" id="state" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                <option value="1" {{ ($state ?? '') == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ ($state ?? '') == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12 col-xl-2">
                            {{-- Label describing the purpose of 'Programa'. --}}
                            <label for="city_program_id" class="form-label">Programa</label>
                            {{-- Dropdown presenting the available options for 'city_program_id'. --}}
                            <select name="city_program_id" id="city_program_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($cityPrograms as $program)
                                    <option value="{{ $program->id }}" {{ (string)($cityProgramId ?? '') === (string)$program->id ? 'selected' : '' }}>
                                        {{ $program->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-2">
                            {{-- Label describing the purpose of 'Registros por página'. --}}
                            <label for="per_page" class="form-label">Registros por página</label>
                            {{-- Dropdown presenting the available options for 'per_page'. --}}
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach($perPageOptions as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-2">
                            {{-- Button element of type 'submit' to trigger the intended action. --}}
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
                <div class="card-header">
                    <h3 class="card-title">Listado de usuarios</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure">{{ $users->total() }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    {{-- Table displays user attributes and quick management actions. --}}
                    <table class="table card-table table-vcenter align-middle" aria-label="Tabla de usuarios">
                        <thead>
                            <tr>
                                <th class="w-1">#</th>
                                <th class="text-truncate" style="max-width: 220px;">Email</th>
                                <th class="text-truncate" style="max-width: 160px;">Rol</th>
                                <th class="text-truncate" style="max-width: 140px;">Estado</th>
                                <th class="text-truncate" style="max-width: 220px;">Nombre</th>
                                <th class="text-truncate" style="max-width: 160px;">Cédula</th>
                                <th class="text-truncate" style="max-width: 240px;">Programa</th>
                                <th class="text-truncate" style="max-width: 160px;">Semestre/Líder</th>
                                <th class="w-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="text-muted">{{ $users->firstItem() + $index }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $user->email }}">{{ $user->email }}</span>
                                </td>
                                <td>
                                    @switch($user->role)
                                        @case('student')
                                            <span class="badge bg-info-lt">Estudiante</span>
                                            @break
                                        @case('professor')
                                            <span class="badge bg-primary-lt">Profesor</span>
                                            @break
                                        @case('committee_leader')
                                            <span class="badge bg-warning-lt">Líder de Comité</span>
                                            @break
                                        @case('research_staff')
                                            <span class="badge bg-success-lt">Personal de Investigación</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary-lt">{{ $user->role }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($user->state === '1' || $user->state === 1)
                                        <span class="badge bg-success-lt">Activo</span>
                                    @else
                                        <span class="badge bg-danger-lt">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->details)
                                        <span class="d-inline-block text-truncate" style="max-width: 220px;" title="{{ $user->details->name }} {{ $user->details->last_name }}">
                                            {{ $user->details->name }} {{ $user->details->last_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->details)
                                        <span class="d-inline-block text-truncate" style="max-width: 160px;" title="{{ $user->details->card_id }}">{{ $user->details->card_id }}</span>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->details && in_array($user->role, ['student', 'professor', 'committee_leader']))
                                        @if(isset($user->details->cityProgram))
                                            {{-- Display both the program and city to contextualize the enrollment. --}}
                                            <span class="d-inline-block text-truncate" style="max-width: 240px;" title="{{ $user->details->cityProgram->program->name ?? 'N/A' }} - {{ $user->details->cityProgram->city->name ?? 'N/A' }}">
                                                {{ $user->details->cityProgram->program->name ?? 'N/A' }} - {{ $user->details->cityProgram->city->name ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin programa</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No aplica</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role === 'student' && $user->details)
                                        <span class="badge bg-blue-lt">Semestre {{ $user->details->semester }}</span>
                                    @elseif(in_array($user->role, ['professor', 'committee_leader']) && $user->details)
                                        <span class="badge bg-secondary-lt">{{ $user->details->committee_leader ? 'Líder' : 'Docente' }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Action button cluster offers view, edit, and state toggles. --}}
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        {{-- Opens the detail profile for additional information. --}}
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="2" />
                                                <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                            </svg>
                                        </a>
                                        {{-- Navigates to the edit form allowing administrators to adjust user data. --}}
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-success" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        @php
                                            $isActive = $user->state == 1 || $user->state === '1';
                                            $statusFormId = $isActive ? 'user-status-form-deactivate-' . $user->id : 'user-status-form-activate-' . $user->id;
                                        @endphp
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="{{ $isActive ? 'Desactivar' : 'Activar' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#user-status-modal"
                                            data-form="{{ $statusFormId }}"
                                            data-user="{{ $user->email }}"
                                            data-action="{{ $isActive ? 'deactivate' : 'activate' }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 7h14" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3h6v3" />
                                            </svg>
                                        </button>
                                        <form id="user-status-form-deactivate-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <form id="user-status-form-activate-{{ $user->id }}" action="{{ route('users.activate', $user) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <circle cx="12" cy="7" r="4" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay usuarios registrados</p>
                                        <p class="empty-subtitle text-muted">Registra usuarios para gestionar el sistema según sus roles.</p>
                                        <div class="empty-action">
                                            <a href="{{ route('register') }}" class="btn btn-primary">Registrar usuario</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    {{-- Summary communicates which slice of users is visible. --}}
                    @if($users->count())
                        <div class="text-muted small">Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} registros</div>
                    @else
                        <div class="text-muted small">No hay registros para los filtros aplicados</div>
                    @endif
                    {{-- Pagination control renders navigation for additional result pages. --}}
                    {{ $users->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal replaces the native confirmation dialog when changing the user status. --}}
    <div class="modal fade" id="user-status-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="user-status-modal-title">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="user-status-modal-message">¿Deseas continuar con esta acción?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="user-status-confirm">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('user-status-modal');
            const modalTitle = document.getElementById('user-status-modal-title');
            const modalMessage = document.getElementById('user-status-modal-message');
            const confirmButton = document.getElementById('user-status-confirm');
            const modalInstance = modalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;
            let formToSubmit = null;

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                formToSubmit = null;
                if (!trigger) {
                    return;
                }

                const action = trigger.getAttribute('data-action');
                const email = trigger.getAttribute('data-user');
                const formId = trigger.getAttribute('data-form');
                formToSubmit = formId ? document.getElementById(formId) : null;

                if (action === 'deactivate') {
                    modalTitle.textContent = 'Desactivar usuario';
                    modalMessage.textContent = `¿Deseas desactivar la cuenta ${email}? Puedes reactivarla más adelante.`;
                    confirmButton.classList.remove('btn-success');
                    confirmButton.classList.add('btn-danger');
                    confirmButton.textContent = 'Desactivar';
                } else {
                    modalTitle.textContent = 'Activar usuario';
                    modalMessage.textContent = `¿Deseas activar la cuenta ${email}? El usuario podrá volver a iniciar sesión.`;
                    confirmButton.classList.remove('btn-danger');
                    confirmButton.classList.add('btn-success');
                    confirmButton.textContent = 'Activar';
                }
            });

            confirmButton?.addEventListener('click', () => {
                if (formToSubmit) {
                    formToSubmit.submit();
                }
                modalInstance?.hide();
            });
        });
    </script>
@endpush
