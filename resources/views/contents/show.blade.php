{{--
    View path: contents/show.blade.php.
    Purpose: Displays detailed information about a specific content record
    using Tablar components.
--}}
@extends('tablar::page')

@php
    use Illuminate\Support\Arr;
    $roleLabels = [
        'research_staff' => 'Equipo de investigación',
        'professor' => 'Profesor',
        'student' => 'Estudiante',
        'committee_leader' => 'Líder de comité',
    ];
    $roles = Arr::wrap($content->roles);
@endphp

@section('title', $content->name)

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('catalog.contents') }}">Contenidos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 12v-7a2 2 0 0 1 2 -2h4l4 4v5" />
                            <rect x="3" y="12" width="18" height="10" rx="2" />
                        </svg>
                        {{ $content->name }}
                    </h2>
                    <p class="text-muted mb-0">Consulta la información detallada del contenido.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('contents.edit', $content) }}" class="btn btn-primary">Editar</a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#content-delete-modal">Eliminar</button>
                        <a href="{{ route('catalog.contents') }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Descripción</h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $content->description ?: 'Este contenido no cuenta con descripción registrada.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Metadatos</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-5 text-secondary">ID</dt>
                                <dd class="col-7">#{{ $content->id }}</dd>
                                <dt class="col-5 text-secondary">Roles</dt>
                                <dd class="col-7">
                                    @forelse($roles as $role)
                                        <span class="badge bg-azure-lt d-inline-flex align-items-center mb-1">{{ $roleLabels[$role] ?? ucfirst(str_replace('_',' ',$role)) }}</span>
                                    @empty
                                        <span class="text-secondary">No definidos</span>
                                    @endforelse
                                </dd>
                                <dt class="col-5 text-secondary">Creado</dt>
                                <dd class="col-7">{{ optional($content->created_at)->format('d/m/Y H:i') ?? '—' }}</dd>
                                <dt class="col-5 text-secondary">Actualizado</dt>
                                <dd class="col-7">{{ optional($content->updated_at)->format('d/m/Y H:i') ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('contents.destroy', $content) }}" id="content-delete-form" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal modal-blur fade" id="content-delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar contenido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Esta acción eliminará el contenido <strong>{{ $content->name }}</strong>. ¿Deseas continuar?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="content-delete-confirm">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteForm = document.getElementById('content-delete-form');
            const confirmButton = document.getElementById('content-delete-confirm');

            if (deleteForm && confirmButton) {
                confirmButton.addEventListener('click', () => {
                    confirmButton.disabled = true;
                    confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...';
                    deleteForm.submit();
                });
            }
        });
    </script>
@endpush
