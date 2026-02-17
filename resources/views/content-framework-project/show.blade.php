{{--
    View path: content-framework-project/show.blade.php.
    Purpose: Displays the details of a project to content framework assignment.
--}}
@extends('tablar::page')

@section('title', 'Detalle de asignación de contenido')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('content-framework-project.index') }}">Asignaciones de contenidos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Asignación #{{ $contentFrameworkProject->id }}</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-info" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="2" />
                            <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                        </svg>
                        Detalle de asignación
                    </h2>
                    <p class="text-muted mb-0">Revisa la información de la relación entre el proyecto y el contenido del marco.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('content-framework-project.edit', $contentFrameworkProject) }}" class="btn btn-outline-primary">Editar</a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#content-framework-project-delete-modal" data-record-name="Asignación #{{ $contentFrameworkProject->id }}" data-destroy-url="{{ route('content-framework-project.destroy', $contentFrameworkProject) }}">Eliminar</button>
                        <a href="{{ route('content-framework-project.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(config('tablar.display_alert'))
                @include('tablar::common.alert')
            @endif

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Información general</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-12 text-secondary">Proyecto</dt>
                                <dd class="col-12 fs-5">
                                    @if($contentFrameworkProject->project)
                                        <a href="{{ route('projects.show', $contentFrameworkProject->project) }}" class="text-decoration-none">
                                            {{ $contentFrameworkProject->project->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">Proyecto no disponible</span>
                                    @endif
                                </dd>
                                <dt class="col-12 text-secondary">Contenido del framework</dt>
                                <dd class="col-12 fs-5">
                                    @if($contentFrameworkProject->contentFramework)
                                        <a href="{{ route('content-frameworks.show', $contentFrameworkProject->contentFramework) }}" class="text-decoration-none">
                                            {{ $contentFrameworkProject->contentFramework->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Contenido no disponible</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Metadatos</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-5 text-secondary">ID</dt>
                                <dd class="col-7">#{{ $contentFrameworkProject->id }}</dd>
                                <dt class="col-5 text-secondary">Creado</dt>
                                <dd class="col-7">{{ $contentFrameworkProject->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                                <dt class="col-5 text-secondary">Actualizado</dt>
                                <dd class="col-7">{{ $contentFrameworkProject->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal handles delete confirmation for the assignment. --}}
    <div class="modal fade" id="content-framework-project-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar asignación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="content-framework-project-delete-message">¿Deseas eliminar esta asignación?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="content-framework-project-delete-form" method="POST">
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
            const modalElement = document.getElementById('content-framework-project-delete-modal');
            const messageElement = document.getElementById('content-framework-project-delete-message');
            const formElement = document.getElementById('content-framework-project-delete-form');

            modalElement?.addEventListener('show.bs.modal', event => {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const recordName = trigger.getAttribute('data-record-name') ?? 'esta asignación';
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
