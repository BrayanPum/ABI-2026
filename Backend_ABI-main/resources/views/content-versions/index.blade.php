{{--
    View path: content-versions/index.blade.php.
    Purpose: Management console for linking contents with project versions via the JSON API.
--}}
@extends('tablar::page')

@section('title', 'Versiones de contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-versions.index') }}">Versiones de contenido</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Listado</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 3h6a2 2 0 0 1 2 2v14l-5 -3l-5 3v-14a2 2 0 0 1 2 -2" />
                        </svg>
                        Versiones de contenido
                        <span class="badge bg-primary ms-2" id="content-version-total">0</span>
                    </h2>
                    <p class="text-muted mb-0">Administra los valores diligenciados para cada contenido dentro de las versiones de proyecto.</p>
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

            <div id="content-version-alert" class="alert d-none" role="alert"></div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Filtros avanzados</h3>
                </div>
                <div class="card-body">
                    <form id="content-version-filters" class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label for="filter-version" class="form-label">Versión</label>
                            <select id="filter-version" class="form-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="filter-content" class="form-label">Contenido</label>
                            <select id="filter-content" class="form-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="filter-project" class="form-label">Proyecto</label>
                            <input type="number" id="filter-project" class="form-control" min="1" placeholder="ID">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="filter-search" class="form-label">Valor</label>
                            <input type="search" id="filter-search" class="form-control" placeholder="Texto contenido">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="filter-per-page" class="form-label">Por página</label>
                            <select id="filter-per-page" class="form-select">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2 d-grid">
                            <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registros diligenciados</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure" id="content-version-summary">Mostrando 0 a 0 de 0 registros</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th class="text-truncate" style="max-width: 200px;">Contenido</th>
                                <th class="text-truncate" style="max-width: 160px;">Versión</th>
                                <th class="text-truncate" style="max-width: 260px;">Proyecto</th>
                                <th class="text-truncate" style="max-width: 320px;">Valor diligenciado</th>
                                <th class="text-truncate" style="max-width: 160px;">Actualizado</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="content-version-rows">
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">Cargando registros...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div class="text-secondary" id="content-version-footer-summary">Mostrando 0 a 0 de 0 registros</div>
                    <nav>
                        <ul class="pagination mb-0" id="content-version-pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-content-version" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-content-version-title">Nuevo registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="content-version-form">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label for="form-version" class="form-label required">Versión</label>
                                <select id="form-version" name="version_id" class="form-select" required>
                                    <option value="">Selecciona una versión</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="form-content" class="form-label required">Contenido</label>
                                <select id="form-content" name="content_id" class="form-select" required>
                                    <option value="">Selecciona un contenido</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="form-value" class="form-label required">Valor diligenciado</label>
                                <textarea id="form-value" name="value" class="form-control" rows="4" required placeholder="Describe la evidencia o valor capturado"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="content-version-submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal replaces the native confirmation when deleting a record. --}}
    <div class="modal fade" id="content-version-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">¿Deseas eliminar <span class="fw-semibold" id="content-version-delete-name">este registro</span>? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="content-version-delete-confirm">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiBase = '{{ url('/api/content-versions') }}';
            const contentsEndpoint = '{{ url('/api/contents') }}';
            const versionsEndpoint = '{{ url('/api/versions') }}';
            const csrfToken = '{{ csrf_token() }}';
            const editUrlTemplate = '{{ route('content-versions.edit', ['contentVersionId' => '__ID__']) }}'.replace('__ID__', ':id');
            const showUrlTemplate = '{{ route('content-versions.show', ['contentVersionId' => '__ID__']) }}'.replace('__ID__', ':id');

            const state = {
                page: 1,
                perPage: 10,
                versionId: null,
                contentId: null,
                projectId: null,
                search: '',
                contents: new Map(),
                versions: new Map(),
            };

            const rows = document.getElementById('content-version-rows');
            const totalBadge = document.getElementById('content-version-total');
            const summaryBadge = document.getElementById('content-version-summary');
            const footerSummary = document.getElementById('content-version-footer-summary');
            const pagination = document.getElementById('content-version-pagination');
            const alertBox = document.getElementById('content-version-alert');

            const filtersForm = document.getElementById('content-version-filters');
            const filterVersion = document.getElementById('filter-version');
            const filterContent = document.getElementById('filter-content');
            const filterProject = document.getElementById('filter-project');
            const filterSearch = document.getElementById('filter-search');
            const filterPerPage = document.getElementById('filter-per-page');

            const modalElement = document.getElementById('modal-content-version');
            const modalTitle = document.getElementById('modal-content-version-title');
            const form = document.getElementById('content-version-form');
            const submitBtn = document.getElementById('content-version-submit');
            const formVersion = document.getElementById('form-version');
            const formContent = document.getElementById('form-content');
            const formValue = document.getElementById('form-value');
            const modalInstance = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(modalElement) : null;

            const deleteModalElement = document.getElementById('content-version-delete-modal');
            const deleteModal = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(deleteModalElement) : null;
            const deleteNameLabel = document.getElementById('content-version-delete-name');
            const deleteConfirmButton = document.getElementById('content-version-delete-confirm');
            let pendingDeleteId = null;
            let pendingDeleteLabel = 'este registro';

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
                if (state.versionId) {
                    params.set('version_id', state.versionId);
                }
                if (state.contentId) {
                    params.set('content_id', state.contentId);
                }
                if (state.projectId) {
                    params.set('project_id', state.projectId);
                }
                if (state.search) {
                    params.set('search', state.search);
                }
                return `${apiBase}?${params.toString()}`;
            }

            function truncate(text, limit = 80) {
                if (!text) return '—';
                return text.length > limit ? `${text.slice(0, limit)}…` : text;
            }

            function renderTable(data) {
                const items = Array.isArray(data?.data) ? data.data : [];
                if (!items.length) {
                    rows.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">No se encontraron registros.</td></tr>';
                } else {
                    rows.innerHTML = items.map(item => {
                        const version = item.version;
                        const project = version?.project;
                        const content = item.content;
                        const updated = item.updated_at ? new Date(item.updated_at).toLocaleString('es-CO') : '—';
                        const projectTitle = project?.title ?? 'Proyecto sin título';
                        const projectLabel = project ? `${projectTitle} (#${project.id ?? '—'})` : 'Sin proyecto';
                        const valuePreview = item.value ? escapeHtml(truncate(item.value, 100)) : '<span class="text-secondary">Sin valor</span>';
                        const contentName = content ? escapeHtml(content.name) : 'Contenido eliminado';
                        const contentNamePlain = content?.name ?? 'Contenido eliminado';
                        const versionLabel = version ? `Versión #${version.id}` : 'Sin versión';
                        const editUrl = editUrlTemplate.replace(':id', item.id);
                        const showUrl = showUrlTemplate.replace(':id', item.id);

                        return `
                            <tr data-id="${item.id}">
                                <td class="text-secondary">#${item.id}</td>
                                <td class="text-truncate" style="max-width: 200px;" title="${contentNamePlain}">${contentName}</td>
                                <td class="text-truncate" style="max-width: 160px;">${escapeHtml(versionLabel)}</td>
                                <td class="text-truncate" style="max-width: 260px;" title="${escapeHtml(projectLabel)}">${escapeHtml(truncate(projectLabel, 60))}</td>
                                <td class="text-truncate" style="max-width: 320px;">${valuePreview}</td>
                                <td class="text-truncate" style="max-width: 160px;">${escapeHtml(updated)}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="${showUrl}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                const from = data?.from ?? 0;
                const to = data?.to ?? 0;
                const total = data?.total ?? 0;
                totalBadge.textContent = total;
                summaryBadge.textContent = `Mostrando ${from} a ${to} de ${total} registros`;
                footerSummary.textContent = `Mostrando ${from} a ${to} de ${total} registros`;

                renderPagination(data?.links || []);
            }

            function renderPagination(links) {
                if (!Array.isArray(links) || !links.length) {
                    pagination.innerHTML = '';
                    return;
                }

                pagination.innerHTML = links.map(link => {
                    const label = link.label
                        .replace('&laquo;', '«')
                        .replace('&raquo;', '»');
                    const disabled = link.url === null ? ' disabled' : '';
                    const active = link.active ? ' active' : '';
                    return `
                        <li class="page-item${disabled}${active}">
                            <a class="page-link" href="#" data-url="${link.url ?? ''}">${label}</a>
                        </li>
                    `;
                }).join('');
            }

            async function fetchContentVersions(url = buildQuery(state.page)) {
                try {
                    clearAlert();
                    rows.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-4">Cargando registros...</td></tr>';
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) {
                        throw new Error('No fue posible cargar los registros.');
                    }
                    const data = await response.json();
                    state.page = data?.current_page ?? 1;
                    renderTable(data);
                } catch (error) {
                    setAlert(error.message || 'Ocurrió un error al cargar los registros.');
                    rows.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Error al cargar los datos.</td></tr>';
                    pagination.innerHTML = '';
                    totalBadge.textContent = '0';
                    summaryBadge.textContent = 'Mostrando 0 a 0 de 0 registros';
                    footerSummary.textContent = 'Mostrando 0 a 0 de 0 registros';
                }
            }

            async function getContentVersion(id) {
                const response = await fetch(`${apiBase}/${id}`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('No fue posible obtener el registro.');
                }
                return response.json();
            }

            async function submitContentVersion(method, url, payload) {
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
                    throw new Error(data.message || 'No fue posible guardar el registro.');
                }

                return response.json().catch(() => ({}));
            }

            async function deleteContentVersion(id) {
                const response = await fetch(`${apiBase}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok && response.status !== 204) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'No fue posible eliminar el registro.');
                }
            }

            async function loadContents() {
                const response = await fetch(`${contentsEndpoint}?per_page=100`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                const options = data?.data ?? [];
                state.contents.clear();
                formContent.innerHTML = '<option value="">Selecciona un contenido</option>';
                filterContent.innerHTML = '<option value="">Todos</option>';
                options.forEach(item => {
                    state.contents.set(String(item.id), item.name);
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = `${item.name} (#${item.id})`;
                    formContent.append(option.cloneNode(true));
                    filterContent.append(option);
                });
            }

            async function loadVersions() {
                const response = await fetch(`${versionsEndpoint}?per_page=100`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                const options = data?.data ?? [];
                state.versions.clear();
                formVersion.innerHTML = '<option value="">Selecciona una versión</option>';
                filterVersion.innerHTML = '<option value="">Todas</option>';
                options.forEach(item => {
                    const label = `Versión #${item.id} · Proyecto ${item.project_id ?? '—'}`;
                    state.versions.set(String(item.id), label);
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = label;
                    formVersion.append(option.cloneNode(true));
                    filterVersion.append(option);
                });
            }

            function resetModalForm() {
                form.reset();
                modalTitle.textContent = 'Nuevo registro';
                submitBtn.textContent = 'Guardar';
            }

            filtersForm.addEventListener('reset', () => {
                setTimeout(() => {
                    state.versionId = null;
                    state.contentId = null;
                    state.projectId = null;
                    state.search = '';
                    state.perPage = Number(filterPerPage.value) || 10;
                    fetchContentVersions(buildQuery(1));
                }, 0);
            });

            filterVersion.addEventListener('change', () => {
                state.versionId = filterVersion.value || null;
                fetchContentVersions(buildQuery(1));
            });

            filterContent.addEventListener('change', () => {
                state.contentId = filterContent.value || null;
                fetchContentVersions(buildQuery(1));
            });

            filterProject.addEventListener('input', event => {
                state.projectId = event.target.value ? Number(event.target.value) : null;
                fetchContentVersions(buildQuery(1));
            });

            filterSearch.addEventListener('input', event => {
                state.search = event.target.value.trim();
                fetchContentVersions(buildQuery(1));
            });

            filterPerPage.addEventListener('change', event => {
                state.perPage = Number(event.target.value) || 10;
                fetchContentVersions(buildQuery(1));
            });

            pagination.addEventListener('click', event => {
                const link = event.target.closest('a[data-url]');
                if (!link) {
                    return;
                }
                event.preventDefault();
                const url = link.getAttribute('data-url');
                if (url) {
                    fetchContentVersions(url);
                }
            });

            rows.addEventListener('click', event => {
                const button = event.target.closest('[data-action]');
                if (!button) {
                    return;
                }

                const id = Number(button.getAttribute('data-id'));
                if (!id) {
                    return;
                }

                if (button.dataset.action === 'edit') {
                    window.location.href = editUrlTemplate.replace(':id', id);
                    return;
                }

                if (button.dataset.action === 'delete') {
                    pendingDeleteId = id;
                    pendingDeleteLabel = button.getAttribute('data-name') || 'este registro';
                    if (deleteNameLabel) {
                        deleteNameLabel.textContent = pendingDeleteLabel;
                    }
                    deleteModal?.show();
                }
            });

            deleteConfirmButton.addEventListener('click', async () => {
                if (!pendingDeleteId) {
                    return;
                }

                deleteConfirmButton.disabled = true;
                try {
                    await deleteContentVersion(pendingDeleteId);
                    deleteModal?.hide();
                    pendingDeleteId = null;
                    pendingDeleteLabel = 'este registro';
                    fetchContentVersions(buildQuery(state.page));
                } catch (error) {
                    setAlert(error.message || 'No fue posible eliminar el registro.');
                } finally {
                    deleteConfirmButton.disabled = false;
                }
            });

            document.getElementById('btn-new-content-version')?.addEventListener('click', () => {
                resetModalForm();
            });

            form.addEventListener('submit', async event => {
                event.preventDefault();
                clearAlert();
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-loading');

                const payload = {
                    content_id: formContent.value ? Number(formContent.value) : null,
                    version_id: formVersion.value ? Number(formVersion.value) : null,
                    value: formValue.value.trim(),
                };

                try {
                    if (!payload.content_id || !payload.version_id || !payload.value) {
                        throw new Error('Completa todos los campos obligatorios.');
                    }

                    await submitContentVersion('POST', apiBase, payload);
                    modalInstance?.hide();
                    resetModalForm();
                    fetchContentVersions(buildQuery(state.page));
                } catch (error) {
                    setAlert(error.message || 'No fue posible guardar el registro.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-loading');
                }
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                resetModalForm();
            });

            (async () => {
                await Promise.all([loadContents(), loadVersions()]);
                fetchContentVersions();
            })();
        });
    </script>
@endpush
