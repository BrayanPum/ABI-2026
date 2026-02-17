{{--
    View path: versions/create.blade.php.
    Purpose: Renders the Tablar-styled create screen for project versions using the shared form.
    This template relies on asynchronous calls to the API to persist data.
--}}
@extends('tablar::page')

@section('title', 'Nueva versión de proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('versions.index') }}">Versiones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear nueva</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Crear versión de proyecto
                    </h2>
                    <p class="text-muted mb-0">Registra una nueva iteración del proyecto para capturar avances y contenidos.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('versions.index') }}" class="btn btn-outline-secondary">
                        Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="version-form-alert" class="alert d-none" role="alert"></div>

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos principales</h3>
                            <div class="card-actions">
                                <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="version-form" novalidate>
                                @include('versions.form')
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('versions.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Guardar versión</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Acciones rápidas</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start gap-2 mb-3">
                                    <span class="avatar bg-azure-lt text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <circle cx="10" cy="10" r="7" />
                                            <line x1="21" y1="21" x2="15" y2="15" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Consulta previa</strong>
                                        <p class="text-secondary mb-0">Utiliza el ID del proyecto para revisar sus contenidos diligenciados.</p>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <span class="avatar bg-azure-lt text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>Sin recarga</strong>
                                        <p class="text-secondary mb-0">El formulario envía los datos mediante la API evitando recargar la página.</p>
                                    </div>
                                </li>
                            </ul>
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
            const apiBase = '{{ url('/api/versions') }}';
            const projectsEndpoint = '{{ url('/api/projects') }}';
            const form = document.getElementById('version-form');
            const projectInput = document.getElementById('project_id');
            const alertBox = document.getElementById('version-form-alert');
            const submitButton = form.querySelector('button[type="submit"]');
            const projectPreview = document.getElementById('project-preview');
            const projectPreviewEmpty = document.getElementById('project-preview-empty');
            const feedbackProject = document.querySelector('[data-feedback-for="project_id"]');

            const previewName = document.getElementById('project-preview-name');
            const previewDescription = document.getElementById('project-preview-description');
            const previewBadge = document.getElementById('project-preview-id');

            function resetFeedback() {
                feedbackProject?.classList.add('d-none');
                feedbackProject && (feedbackProject.textContent = '');
                projectInput.classList.remove('is-invalid');
            }

            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
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
                        projectPreviewEmpty.textContent = 'No se pudo encontrar información para el proyecto indicado.';
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
                    projectPreviewEmpty.textContent = 'No fue posible consultar el proyecto. Verifica tu conexión.';
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
                    const response = await fetch(apiBase, {
                        method: 'POST',
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

                        showAlert('danger', body.message ?? 'No fue posible crear la versión.');
                        alertBox.classList.remove('d-none');
                        return;
                    }

                    showAlert('success', body.message ?? 'Versión creada correctamente. Redirigiendo...');
                    alertBox.classList.remove('d-none');

                    setTimeout(() => {
                        window.location.href = '{{ route('versions.index') }}';
                    }, 1200);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error inesperado. Intenta nuevamente.');
                    alertBox.classList.remove('d-none');
                } finally {
                    submitButton.disabled = false;
                    submitButton.classList.remove('btn-loading');
                }
            });
        });
    </script>
@endpush

@push('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .08);
        }
        .avatar {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
@endpush
