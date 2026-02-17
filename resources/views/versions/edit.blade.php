{{--
    View path: versions/edit.blade.php.
    Purpose: Presents the Tablar-styled edit screen for project versions using the shared form partial.
    Expected variables:
    - $versionId (int): identifier of the version to edit, provided via the route closure.
--}}
@extends('tablar::page')

@section('title', 'Editar versión de proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('versions.index') }}">Versiones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M13 20l7 -7" />
                            <path d="M9 4l11 0" />
                            <path d="M9 8l11 0" />
                            <path d="M9 12l11 0" />
                            <path d="M9 16l11 0" />
                            <path d="M5 4l0 .01" />
                            <path d="M5 8l0 .01" />
                            <path d="M5 12l0 .01" />
                            <path d="M5 16l0 .01" />
                        </svg>
                        Editar versión #{{ $versionId }}
                    </h2>
                    <p class="text-muted mb-0">Actualiza la referencia del proyecto asociado a esta versión.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('versions.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="version-edit-alert" class="alert d-none" role="alert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos de la versión</h3>
                    <div class="card-actions">
                        <span class="badge bg-azure">ID {{ $versionId }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form id="version-edit-form" novalidate>
                        @include('versions.form')
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('versions.index') }}" class="btn btn-link">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const versionId = {{ (int) $versionId }};
            const apiBase = '{{ url('/api/versions') }}';
            const projectsEndpoint = '{{ url('/api/projects') }}';

            const form = document.getElementById('version-edit-form');
            const projectInput = document.getElementById('project_id');
            const alertBox = document.getElementById('version-edit-alert');
            const submitButton = form.querySelector('button[type="submit"]');
            const feedbackProject = document.querySelector('[data-feedback-for="project_id"]');
            const projectPreview = document.getElementById('project-preview');
            const projectPreviewEmpty = document.getElementById('project-preview-empty');
            const previewName = document.getElementById('project-preview-name');
            const previewDescription = document.getElementById('project-preview-description');
            const previewBadge = document.getElementById('project-preview-id');

            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
            }

            function resetFeedback() {
                feedbackProject?.classList.add('d-none');
                feedbackProject && (feedbackProject.textContent = '');
                projectInput.classList.remove('is-invalid');
            }

            async function fetchProject(projectId) {
                if (!projectId) {
                    projectPreview.hidden = true;
                    projectPreviewEmpty.classList.remove('d-none');
                    return;
                }

                try {
                    const response = await fetch(`${projectsEndpoint}/${projectId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        projectPreview.hidden = true;
                        projectPreviewEmpty.classList.remove('d-none');
                        projectPreviewEmpty.textContent = 'No se encontró información adicional del proyecto.';
                        return;
                    }

                    const data = await response.json();
                    projectPreview.hidden = false;
                    projectPreviewEmpty.classList.add('d-none');
                    previewBadge.textContent = `ID ${projectId}`;
                    previewName.textContent = data.name ?? 'Proyecto sin nombre';
                    previewDescription.textContent = data.code ? `Código interno: ${data.code}` : 'Sin código registrado.';
                } catch (error) {
                    projectPreview.hidden = true;
                    projectPreviewEmpty.classList.remove('d-none');
                    projectPreviewEmpty.textContent = 'No fue posible cargar los datos del proyecto.';
                }
            }

            async function loadVersion() {
                try {
                    const response = await fetch(`${apiBase}/${versionId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        showAlert('danger', 'No se pudo cargar la información de la versión solicitada.');
                        alertBox.classList.remove('d-none');
                        submitButton.disabled = true;
                        return;
                    }

                    const data = await response.json();
                    projectInput.value = data.project_id ?? '';
                    previewBadge.textContent = `ID ${versionId}`;
                    fetchProject(projectInput.value);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error al cargar los datos.');
                    alertBox.classList.remove('d-none');
                    submitButton.disabled = true;
                }
            }

            projectInput.addEventListener('change', () => fetchProject(projectInput.value));
            projectInput.addEventListener('blur', () => fetchProject(projectInput.value));

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                resetFeedback();
                alertBox.classList.add('d-none');

                const payload = {
                    project_id: projectInput.value ? Number(projectInput.value) : null,
                };

                if (!payload.project_id) {
                    projectInput.classList.add('is-invalid');
                    feedbackProject?.classList.remove('d-none');
                    if (feedbackProject) {
                        feedbackProject.textContent = 'Debes indicar un ID de proyecto válido.';
                    }
                    return;
                }

                submitButton.disabled = true;
                submitButton.classList.add('btn-loading');

                try {
                    const response = await fetch(`${apiBase}/${versionId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const body = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (response.status === 422 && body?.errors?.project_id) {
                            projectInput.classList.add('is-invalid');
                            feedbackProject?.classList.remove('d-none');
                            feedbackProject && (feedbackProject.textContent = body.errors.project_id[0]);
                        }

                        showAlert('danger', body.message ?? 'No fue posible actualizar la versión.');
                        alertBox.classList.remove('d-none');
                        return;
                    }

                    showAlert('success', body.message ?? 'Versión actualizada correctamente.');
                    alertBox.classList.remove('d-none');
                    setTimeout(() => {
                        window.location.href = '{{ route('versions.index') }}';
                    }, 1200);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error inesperado al guardar.');
                    alertBox.classList.remove('d-none');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-loading');
                }
            });

            loadVersion();
        });
    </script>
@endpush

@push('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .08);
        }
    </style>
@endpush
