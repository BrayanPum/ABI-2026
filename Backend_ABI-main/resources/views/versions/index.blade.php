{{--
    View path: versions/index.blade.php.
    Purpose: Provides a Tablar-styled dashboard to manage project versions
    through the existing JSON API endpoints.
--}}
@extends('tablar::page')

@section('title', 'Gestión de Versiones')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Versiones</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 6v6l4 2" />
                            <path d="M4 12a8 8 0 1 0 16 0a8 8 0 0 0 -16 0" />
                        </svg>
                        Gestión de Versiones de Proyecto
                    </h2>
                    <p class="text-muted mb-0">Registra y administra las diferentes iteraciones de tus proyectos.</p>
                </div>
                <div class="col-auto ms-auto d-print-none"></div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div id="version-alert" class="alert d-none" role="alert"></div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Filtros de búsqueda</h3>
                </div>
                <div class="card-body">
                    <form id="version-filters" class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label for="project-id" class="form-label">ID del proyecto</label>
                            <input type="number" id="project-id" class="form-control" min="1" placeholder="Ej. 12">
                            
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="version-per-page" class="form-label">Por página</label>
                            <select id="version-per-page" class="form-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <button type="reset" class="btn btn-outline-secondary w-100">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de versiones</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure" id="version-total">0</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter align-middle" aria-label="Tabla de versiones" id="version-table">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th class="text-truncate" style="max-width: 260px;">Proyecto</th>
                                <th class="text-truncate" style="max-width: 160px;">Creada</th>
                                <th class="text-truncate" style="max-width: 160px;">Actualizada</th>
                                <th class="w-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="version-rows">
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">Cargando versiones...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div class="text-secondary" id="version-summary">Mostrando 0 a 0 de 0 registros</div>
                    <nav>
                        <ul class="pagination mb-0" id="version-pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal used to create or update versions through the API. --}}
    <div class="modal modal-blur fade" id="modal-version" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-version-title">Nueva versión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="version-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="version-project" class="form-label required">ID del proyecto asociado</label>
                            <input type="number" id="version-project" name="project_id" class="form-control" min="1" required placeholder="Ingresa el identificador del proyecto">
                            <small class="form-hint">Verifica que el proyecto exista antes de crear la versión.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="version-submit">Guardar versión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal replaces the native confirmation dialog when deleting a version. --}}
    <div class="modal fade" id="version-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar versión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Deseas eliminar <span class="fw-semibold" id="version-delete-name">esta versión</span>? Esta acción es irreversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="version-delete-confirm">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiBase = '{{ url('/api/versions') }}';
            const csrfToken = '{{ csrf_token() }}';
            const showUrlTemplate = '{{ url('versions') }}/:id';
            const editUrlTemplate = '{{ url('versions') }}/:id/edit';

            const state = {
                page: 1,
                perPage: 10,
                projectId: null,
                currentId: null,
                searchTitle: '',
                programId: null,
            };

            const icons = {
                view: `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="2" />
                        <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                    </svg>
                `,
                edit: `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                `,
                delete: `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 7h14" />
                        <path d="M10 11v6" />
                        <path d="M14 11v6" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3h6v3" />
                    </svg>
                `,
            };

            const rows = document.getElementById('version-rows');
            const totalBadge = document.getElementById('version-total');
            const summary = document.getElementById('version-summary');
            const pagination = document.getElementById('version-pagination');
            const alertBox = document.getElementById('version-alert');

            const filtersForm = document.getElementById('version-filters');
            const projectInput = document.getElementById('project-id');
            const perPageSelect = document.getElementById('version-per-page');

            const modalElement = document.getElementById('modal-version');
            const modalTitle = document.getElementById('modal-version-title');
            const form = document.getElementById('version-form');
            const submitBtn = document.getElementById('version-submit');
            const projectField = document.getElementById('version-project');
            const modalInstance = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;

            const deleteModalElement = document.getElementById('version-delete-modal');
            const deleteModal = deleteModalElement && window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(deleteModalElement) : null;
            const deleteNameLabel = document.getElementById('version-delete-name');
            const deleteConfirmButton = document.getElementById('version-delete-confirm');
            let deleteTargetId = null;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function setAlert(message, type = 'danger') {
                alertBox.textContent = message;
                alertBox.className = `alert alert-${type}`;
            }

            function clearAlert() {
                alertBox.textContent = '';
                alertBox.className = 'alert d-none';
            }

            function buildQuery(page = 1) {
                const params = new URLSearchParams();

                params.set('page', page);
                params.set('per_page', state.perPage);

                if (state.projectId) {
                    params.set('project_id', state.projectId);
                }

                // Nuevo filtro: título
                if (state.searchTitle && state.searchTitle.trim() !== '') {
                    params.set('title', state.searchTitle.trim());
                }

                // Nuevo filtro: programa
                if (state.programId) {
                    params.set('program_id', state.programId);
                }

                return `${apiBase}?${params.toString()}`;
            }

            filtersForm.addEventListener('submit', (e) => {
                e.preventDefault();

                // Nuevos filtros
                state.searchTitle = document.getElementById('filter-title')?.value || '';
                state.programId = document.getElementById('filter-program')?.value || '';

                state.page = 1;
                fetchVersions(buildQuery());
            });


            function renderTable(data) {
                const items = Array.isArray(data?.data) ? data.data : [];
                if (!items.length) {
                    rows.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-4">No se encontraron versiones.</td></tr>';
                } else {
                    rows.innerHTML = items.map(item => {
                        const created = item.created_at ? new Date(item.created_at).toLocaleString('es-CO') : '—';
                        const updated = item.updated_at ? new Date(item.updated_at).toLocaleString('es-CO') : '—';
                        const projectTitle = item.project?.title ? escapeHtml(item.project.title) : 'Proyecto sin título';
                        const projectLabel = item.project ? `${projectTitle} (#${item.project.id ?? '—'})` : 'Sin proyecto';
                        const projectHtml = `<span class="d-inline-block text-truncate" style="max-width: 260px;" title="${projectLabel}">${projectLabel}</span>`;
                        const createdHtml = `<span class="d-inline-block text-truncate" style="max-width: 160px;" title="${escapeHtml(created)}">${escapeHtml(created)}</span>`;
                        const updatedHtml = `<span class="d-inline-block text-truncate" style="max-width: 160px;" title="${escapeHtml(updated)}">${escapeHtml(updated)}</span>`;
                        const showUrl = showUrlTemplate.replace(':id', item.id);
                        const editUrl = editUrlTemplate.replace(':id', item.id);
                        const safeDeleteLabel = escapeHtml(`versión #${item.id}`);

                        return `
                            <tr data-id="${item.id}">
                                <td class="text-secondary">#${item.id}</td>
                                <td>${projectHtml}</td>
                                <td>${createdHtml}</td>
                                <td>${updatedHtml}</td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        <a class="btn btn-sm btn-outline-primary" href="${showUrl}" title="Ver">${icons.view}</a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                totalBadge.textContent = data?.total ?? 0;
                const from = data?.from ?? 0;
                const to = data?.to ?? 0;
                const total = data?.total ?? 0;
                summary.textContent = `Mostrando ${from} a ${to} de ${total} registros`;

                renderPagination(data?.links || []);
            }

            function renderPagination(links) {
                if (!Array.isArray(links) || !links.length) {
                    pagination.innerHTML = '';
                    return;
                }

                pagination.innerHTML = links.map(link => {
                    let label = link.label
                        .replace('&laquo;', '«')
                        .replace('&raquo;', '»');
                    const normalized = label.toLowerCase();
                    if (normalized.includes('previous')) {
                        label = '‹';
                    } else if (normalized.includes('next')) {
                        label = '›';
                    }
                    const disabled = link.url === null ? ' disabled' : '';
                    const active = link.active ? ' active' : '';
                    return `
                        <li class="page-item${disabled}${active}">
                            <a class="page-link" href="#" data-url="${link.url ?? ''}">${label}</a>
                        </li>
                    `;
                }).join('');
            }

            async function fetchVersions(url = buildQuery(state.page)) {
                try {
                    clearAlert();
                    rows.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-4">Cargando versiones...</td></tr>';
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) {
                        throw new Error('No fue posible cargar las versiones.');
                    }
                    const data = await response.json();
                    state.page = data?.current_page ?? 1;
                    renderTable(data);
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error al cargar las versiones.');
                    rows.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar los datos.</td></tr>';
                    pagination.innerHTML = '';
                    totalBadge.textContent = '0';
                    summary.textContent = 'Mostrando 0 a 0 de 0 registros';
                }
            }

            async function submitVersion(method, url, payload) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    const firstError = Object.values(data.errors || {})[0]?.[0] ?? 'Datos inválidos.';
                    throw new Error(firstError);
                }

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible guardar la versión.');
                }

                return response.json().catch(() => ({}));
            }

            async function deleteVersion(id) {
                const response = await fetch(`${apiBase}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (response.status === 409) {
                    const data = await response.json();
                    throw new Error(data.message || 'La versión no puede eliminarse porque tiene contenidos asociados.');
                }

                if (!response.ok && response.status !== 204) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible eliminar la versión.');
                }
            }

            filtersForm.addEventListener('submit', event => event.preventDefault());

            filtersForm.addEventListener('reset', () => {
                setTimeout(() => {
                    state.projectId = null;
                    state.perPage = 10;
                    state.page = 1;
                    perPageSelect.value = '10';
                    fetchVersions(buildQuery());
                }, 0);
            });

            projectInput.addEventListener('change', () => {
                const value = projectInput.value.trim();
                state.projectId = value ? Number(value) : null;
                state.page = 1;
                fetchVersions(buildQuery());
            });

            perPageSelect.addEventListener('change', () => {
                state.perPage = Number(perPageSelect.value) || 10;
                state.page = 1;
                fetchVersions(buildQuery());
            });

            pagination.addEventListener('click', event => {
                const link = event.target.closest('a[data-url]');
                if (!link) return;
                event.preventDefault();
                const url = link.getAttribute('data-url');
                if (!url) return;
                fetchVersions(url);
            });

            rows.addEventListener('click', async event => {
                const button = event.target.closest('button[data-action]');
                if (!button) return;
                const id = button.getAttribute('data-id');
                if (!id) return;

                if (button.dataset.action === 'delete') {
                    deleteTargetId = id;
                    const label = button.getAttribute('data-title') || `versión #${id}`;
                    deleteNameLabel.textContent = label;
                    deleteModal?.show();
                }
            });

            deleteConfirmButton?.addEventListener('click', async () => {
                if (!deleteTargetId) {
                    return;
                }
                try {
                    clearAlert();
                    await deleteVersion(deleteTargetId);
                    setAlert('Versión eliminada correctamente.', 'success');
                    fetchVersions(buildQuery(state.page));
                } catch (error) {
                    setAlert(error.message || 'No fue posible eliminar la versión.');
                } finally {
                    deleteModal?.hide();
                    deleteTargetId = null;
                }
            });

            modalElement.addEventListener('show.bs.modal', () => {
                if (!state.currentId) {
                    modalTitle.textContent = 'Nueva versión';
                    submitBtn.textContent = 'Guardar versión';
                    projectField.value = '';
                }
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                state.currentId = null;
                projectField.value = '';
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();
                try {
                    clearAlert();
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

                    const projectId = Number(projectField.value);
                    if (!projectId) {
                        throw new Error('Debes indicar el ID de un proyecto válido.');
                    }

                    let method = 'POST';
                    let url = apiBase;
                    const payload = { project_id: projectId };

                    if (state.currentId) {
                        method = 'PUT';
                        url = `${apiBase}/${state.currentId}`;
                    }

                    await submitVersion(method, url, payload);
                    modalInstance?.hide();
                    setAlert(state.currentId ? 'Versión actualizada correctamente.' : 'Versión creada con éxito.', 'success');
                    fetchVersions(buildQuery());
                } catch (error) {
                    setAlert(error.message || 'No fue posible guardar la versión.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = state.currentId ? 'Actualizar versión' : 'Guardar versión';
                }
            });

            const newBtn = document.getElementById('btn-new-version');
            if (newBtn) {
                newBtn.addEventListener('click', () => {
                    state.currentId = null;
                    modalTitle.textContent = 'Nueva versión';
                    submitBtn.textContent = 'Guardar versión';
                    projectField.value = '';
                });
            }

            fetchVersions(buildQuery());
        });
    </script>
@endpush

@push('css')
    <style>
        .form-label.required::after {
            content: ' *';
            color: var(--tblr-danger);
        }
    </style>
@endpush
