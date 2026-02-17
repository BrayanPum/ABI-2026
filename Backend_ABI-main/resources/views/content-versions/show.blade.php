{{--
    View path: content-versions/show.blade.php.
    Purpose: Displays the detail view for a content-version record retrieved via the JSON API.
    Expected variables: $contentVersionId (int).
--}}
@extends('tablar::page')

@section('title', 'Detalle de versión de contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-versions.index') }}">Versiones de contenido</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 3h6a2 2 0 0 1 2 2v14l-5 -3l-5 3v-14a2 2 0 0 1 2 -2" />
                        </svg>
                        Registro #{{ $contentVersionId }}
                    </h2>
                    <p class="text-muted mb-0">Consulta la información completa del contenido diligenciado en una versión.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-versions.edit', ['contentVersionId' => $contentVersionId]) }}" class="btn btn-outline-primary">Editar</a>
                        <a href="{{ route('content-versions.index') }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="content-version-show-alert" class="alert d-none" role="alert"></div>

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Contenido</h3>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" id="content-name">Cargando...</h3>
                            <p class="text-secondary" id="content-description">—</p>
                            <div class="mt-3">
                                <span class="badge bg-azure-lt" id="content-id-badge">ID —</span>
                                <span class="badge bg-azure-lt" id="content-roles">Roles: —</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Versión</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-5 text-secondary">Versión</dt>
                                <dd class="col-7" id="version-info">—</dd>
                                <dt class="col-5 text-secondary">Proyecto</dt>
                                <dd class="col-7" id="version-project">—</dd>
                                <dt class="col-5 text-secondary">Valor</dt>
                                <dd class="col-7" id="content-value">—</dd>
                                <dt class="col-5 text-secondary">Actualizado</dt>
                                <dd class="col-7" id="content-updated">—</dd>
                            </dl>
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

            const alertBox = document.getElementById('content-version-show-alert');
            const contentName = document.getElementById('content-name');
            const contentDescription = document.getElementById('content-description');
            const contentBadge = document.getElementById('content-id-badge');
            const contentRoles = document.getElementById('content-roles');

            const versionInfo = document.getElementById('version-info');
            const versionProject = document.getElementById('version-project');
            const contentValue = document.getElementById('content-value');
            const contentUpdated = document.getElementById('content-updated');

            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            function formatRoles(roles) {
                if (!Array.isArray(roles) || roles.length === 0) {
                    return 'Sin roles asignados';
                }
                return roles.map((role) => role.replace(/_/g, ' ')).join(', ');
            }

            function formatDate(value) {
                if (!value) return '—';
                try {
                    const date = new Date(value);
                    return new Intl.DateTimeFormat('es-ES', {
                        dateStyle: 'medium',
                        timeStyle: 'short'
                    }).format(date);
                } catch (error) {
                    return value;
                }
            }

            async function loadRecord() {
                try {
                    const response = await fetch(`${apiBase}/${recordId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        showAlert('danger', 'No se pudo obtener la información del registro solicitado.');
                        return;
                    }

                    const data = await response.json();
                    const content = data.content ?? {};
                    const version = data.version ?? {};

                    contentName.textContent = content.name ?? 'Contenido sin nombre';
                    contentDescription.textContent = content.description ?? 'No se registró una descripción para este contenido.';
                    contentBadge.textContent = `ID ${content.id ?? '—'}`;
                    contentRoles.textContent = `Roles: ${formatRoles(content.roles ?? [])}`;

                    versionInfo.textContent = `Versión #${version.id ?? '—'}`;
                    versionProject.textContent = version.project ? `${version.project.name ?? 'Proyecto sin nombre'} (#${version.project.id})` : `ID ${version.project_id ?? '—'}`;
                    contentValue.textContent = data.value ?? '—';
                    contentUpdated.textContent = formatDate(data.updated_at);
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error al consultar el registro.');
                }
            }

            loadRecord();
        });
    </script>
@endpush
