{{--
    View path: content-versions/edit.blade.php.
    Purpose: Presents the edit view for a content-version record using the shared form fragment.
--}}
@extends('tablar::page')

@section('title', 'Editar versión de contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-versions.index') }}">Versiones de contenido</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 4h6a2 2 0 0 1 2 2v14l-5 -3l-5 3v-14a2 2 0 0 1 2 -2" />
                        </svg>
                        Editar registro #{{ $contentVersionId }}
                    </h2>
                    <p class="text-muted mb-0">Modifica la relación entre el contenido y la versión del proyecto.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('content-versions.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="content-version-edit-alert" class="alert d-none" role="alert"></div>

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos del registro</h3>
                            <div class="card-actions">
                                <span class="badge bg-azure">ID {{ $contentVersionId }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="content-version-edit-form" novalidate>
                                @include('content-versions.form')
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('content-versions.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const recordId = {{ (int) $contentVersionId }};
            const apiBase = '{{ url('/api/content-versions') }}';
            const contentsEndpoint = '{{ url('/api/contents') }}';
            const versionsEndpoint = '{{ url('/api/versions') }}';

            const form = document.getElementById('content-version-edit-form');
            const alertBox = document.getElementById('content-version-edit-alert');
            const submitButton = form.querySelector('button[type="submit"]');

            const contentSelect = document.getElementById('content_id');
            const versionSelect = document.getElementById('version_id');
            const valueField = document.getElementById('value');

            const feedbackContent = document.querySelector('[data-feedback-for="content_id"]');
            const feedbackVersion = document.querySelector('[data-feedback-for="version_id"]');
            const feedbackValue = document.querySelector('[data-feedback-for="value"]');

            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            function resetFeedback() {
                [contentSelect, versionSelect, valueField].forEach((element) => element.classList.remove('is-invalid'));
                [feedbackContent, feedbackVersion, feedbackValue].forEach((element) => {
                    if (element) {
                        element.classList.add('d-none');
                        element.textContent = '';
                    }
                });
            }

            async function loadSelect(selectElement, endpoint, labelResolver, selectedValue) {
                selectElement.disabled = true;
                try {
                    const response = await fetch(`${endpoint}?per_page=100`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        selectElement.innerHTML = '<option value="">No fue posible cargar las opciones</option>';
                        return;
                    }

                    const data = await response.json();
                    const items = Array.isArray(data.data) ? data.data : [];
                    const placeholder = selectElement.firstElementChild?.textContent || 'Selecciona una opción';
                    selectElement.innerHTML = `<option value="">${placeholder}</option>`;

                    items.forEach((item) => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = labelResolver(item);
                        if (String(item.id) === String(selectedValue)) {
                            option.selected = true;
                        }
                        selectElement.appendChild(option);
                    });
                } catch (error) {
                    selectElement.innerHTML = '<option value="">No fue posible cargar las opciones</option>';
                } finally {
                    selectElement.disabled = false;
                }
            }

            async function loadRecord() {
                try {
                    const response = await fetch(`${apiBase}/${recordId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        showAlert('danger', 'No se pudo cargar el registro solicitado.');
                        submitButton.disabled = true;
                        return;
                    }

                    const data = await response.json();
                    valueField.value = data.value ?? '';

                    await loadSelect(contentSelect, contentsEndpoint, (item) => item.name ?? `Contenido #${item.id}`, data.content_id);
                    await loadSelect(versionSelect, versionsEndpoint, (item) => `Versión #${item.id} · Proyecto ${item.project_id ?? '—'}`, data.version_id);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error al cargar el registro.');
                    submitButton.disabled = true;
                }
            }

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                resetFeedback();
                alertBox.classList.add('d-none');

                const payload = {
                    content_id: contentSelect.value ? Number(contentSelect.value) : null,
                    version_id: versionSelect.value ? Number(versionSelect.value) : null,
                    value: valueField.value.trim(),
                };

                let hasError = false;
                if (!payload.content_id) {
                    contentSelect.classList.add('is-invalid');
                    feedbackContent?.classList.remove('d-none');
                    feedbackContent && (feedbackContent.textContent = 'Selecciona un contenido.');
                    hasError = true;
                }
                if (!payload.version_id) {
                    versionSelect.classList.add('is-invalid');
                    feedbackVersion?.classList.remove('d-none');
                    feedbackVersion && (feedbackVersion.textContent = 'Selecciona una versión.');
                    hasError = true;
                }
                if (!payload.value) {
                    valueField.classList.add('is-invalid');
                    feedbackValue?.classList.remove('d-none');
                    feedbackValue && (feedbackValue.textContent = 'Debes ingresar un valor.');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.classList.add('btn-loading');

                try {
                    const response = await fetch(`${apiBase}/${recordId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const body = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const errors = body.errors ?? {};
                        if (errors.content_id) {
                            contentSelect.classList.add('is-invalid');
                            feedbackContent?.classList.remove('d-none');
                            feedbackContent && (feedbackContent.textContent = errors.content_id[0]);
                        }
                        if (errors.version_id) {
                            versionSelect.classList.add('is-invalid');
                            feedbackVersion?.classList.remove('d-none');
                            feedbackVersion && (feedbackVersion.textContent = errors.version_id[0]);
                        }
                        if (errors.value) {
                            valueField.classList.add('is-invalid');
                            feedbackValue?.classList.remove('d-none');
                            feedbackValue && (feedbackValue.textContent = errors.value[0]);
                        }
                        const message = body.message ?? 'No fue posible guardar los cambios.';
                        showAlert('danger', message);
                        return;
                    }

                    showAlert('success', 'Los cambios se guardaron correctamente.');
                } catch (error) {
                    showAlert('danger', error.message || 'No fue posible guardar los cambios.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-loading');
                }
            });

            loadRecord();
        });
    </script>
@endpush
