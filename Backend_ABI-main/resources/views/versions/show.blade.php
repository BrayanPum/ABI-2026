{{--
    View path: versions/show.blade.php.
    Purpose: Displays a Tablar-styled detail page for a project version using API data.
    Expected variables:
    - $versionId (int): identifier of the version provided by the route closure.
--}}
@extends('tablar::page')

@section('title', 'Detalle de versión')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('versions.index') }}">Versiones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 4h18" />
                            <path d="M4 8h16" />
                            <path d="M5 12h14" />
                            <path d="M6 16h12" />
                            <path d="M8 20h8" />
                        </svg>
                        Versión #{{ $versionId }}
                    </h2>
                    <p class="text-muted mb-0">Consulta la información general y los contenidos diligenciados en esta versión.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('versions.edit', ['versionId' => $versionId]) }}" class="btn btn-primary" id="version-edit-link">Editar</a>
                        <a href="{{ route('versions.index') }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div id="version-show-alert" class="alert d-none" role="alert"></div>

            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Resumen</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0" id="version-metadata">
                                <dt class="col-5 text-secondary">ID</dt>
                                <dd class="col-7">#{{ $versionId }}</dd>
                                <dt class="col-5 text-secondary">Proyecto</dt>
                                <dd class="col-7" id="version-project">Cargando...</dd>
                                <dt class="col-5 text-secondary">Creado</dt>
                                <dd class="col-7" id="version-created">—</dd>
                                <dt class="col-5 text-secondary">Actualizado</dt>
                                <dd class="col-7" id="version-updated">—</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Contenidos diligenciados</h3>
                            <div class="card-actions">
                                <span class="badge bg-azure" id="version-contents-count">0</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th class="w-1">ID</th>
                                        <th>Contenido</th>
                                        <th>Valor diligenciado</th>
                                    </tr>
                                </thead>
                                <tbody id="version-contents-body">
                                    <tr>
                                        <td colspan="3" class="text-center text-secondary py-4">Cargando contenidos...</td>
                                    </tr>
                                </tbody>
                            </table>
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
            const versionId = {{ (int) $versionId }};
            const apiBase = '{{ url('/api/versions') }}';
            const alertBox = document.getElementById('version-show-alert');
            const projectElement = document.getElementById('version-project');
            const createdElement = document.getElementById('version-created');
            const updatedElement = document.getElementById('version-updated');
            const contentsBody = document.getElementById('version-contents-body');
            const contentsBadge = document.getElementById('version-contents-count');

            function showAlert(type, message) {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
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

            async function loadVersion() {
                try {
                    const response = await fetch(`${apiBase}/${versionId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        showAlert('danger', 'No se pudo obtener la información de la versión solicitada.');
                        contentsBody.innerHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">No fue posible cargar los contenidos.</td></tr>';
                        return;
                    }

                    const data = await response.json();

                    projectElement.textContent = data.project ? `${data.project.title ?? 'Proyecto sin nombre'} (#${data.project.id})` : `ID ${data.project_id ?? '—'}`;
                    createdElement.textContent = formatDate(data.created_at);
                    updatedElement.textContent = formatDate(data.updated_at);

                    if (Array.isArray(data.contents) && data.contents.length) {
                        contentsBadge.textContent = data.contents.length;
                        contentsBody.innerHTML = '';
                        data.contents.forEach((content) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${content.id}</td>
                                <td>
                                    <strong>${content.name ?? 'Sin nombre'}</strong>
                                    <div class="text-secondary small">${content.description ?? 'Sin descripción disponible.'}</div>
                                </td>
                                <td>${content.pivot?.value ?? '—'}</td>
                            `;
                            contentsBody.appendChild(row);
                        });
                    } else {
                        contentsBadge.textContent = '0';
                        contentsBody.innerHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">No hay contenidos asociados a esta versión.</td></tr>';
                    }
                } catch (error) {
                    showAlert('danger', 'Ocurrió un error al consultar la versión.');
                    contentsBody.innerHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">No fue posible cargar los contenidos.</td></tr>';
                }
            }

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
