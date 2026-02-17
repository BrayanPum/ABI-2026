{{--
    View path: framework/edit.blade.php.
    Purpose: Displays the Tablar-styled edit screen for academic frameworks using the shared form.
    Expected variables within this template: $framework.
--}}
@extends('tablar::page')

@section('title', 'Editar marco')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Marcos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Editar marco #{{ $framework->id }}
                    </h2>
                    <p class="text-muted mb-0">Actualiza la información del marco y su período de vigencia.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('frameworks.show', $framework) }}" class="btn btn-outline-primary">Ver detalle</a>
                    <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Datos generales</h3>
                    <div class="card-actions">
                        <span class="badge bg-success">ID {{ $framework->id }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('frameworks.update', $framework) }}" id="framework-edit-form" autocomplete="off">
                        @csrf
                        @method('PATCH')
                        @include('framework.form', ['framework' => $framework, 'showSubmit' => false])

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-link" id="framework-edit-cancel" data-cancel-url="{{ route('frameworks.index') }}">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal replaces the native confirmation when discarding unsaved changes. --}}
    <div class="modal fade" id="framework-cancel-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Descartar cambios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Se perderán las modificaciones no guardadas. ¿Deseas salir?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Seguir editando</button>
                    <a href="{{ route('frameworks.index') }}" class="btn btn-primary" id="framework-cancel-confirm">Salir sin guardar</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startYearInput = document.getElementById('start_year');
            const endYearInput = document.getElementById('end_year');
            if (startYearInput && endYearInput) {
                startYearInput.addEventListener('change', () => {
                    const startYear = Number.parseInt(startYearInput.value, 10);
                    if (!Number.isNaN(startYear)) {
                        endYearInput.min = String(startYear);
                        const endYear = Number.parseInt(endYearInput.value, 10);
                        if (!Number.isNaN(endYear) && endYear < startYear) {
                            endYearInput.value = '';
                        }
                    }
                });
            }

            const form = document.getElementById('framework-edit-form');
            const cancelButton = document.getElementById('framework-edit-cancel');
            const cancelModalElement = document.getElementById('framework-cancel-modal');
            const cancelModal = cancelModalElement && window.bootstrap ? new window.bootstrap.Modal(cancelModalElement) : null;
            const cancelUrl = cancelButton?.getAttribute('data-cancel-url');
            let formChanged = false;

            form?.addEventListener('input', () => {
                formChanged = true;
            });

            cancelButton?.addEventListener('click', event => {
                if (!formChanged && cancelUrl) {
                    window.location.href = cancelUrl;
                    return;
                }

                if (cancelModal) {
                    event.preventDefault();
                    cancelModal.show();
                }
            });
        });
    </script>
@endpush
