{{--
    View path: framework/create.blade.php.
    Purpose: Presents the Tablar-styled create screen for academic frameworks using the shared form.
--}}
@extends('tablar::page')

@section('title', 'Registrar marco')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('frameworks.index') }}">Marcos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <line x1="9" y1="9" x2="15" y2="9" />
                            <line x1="9" y1="15" x2="15" y2="15" />
                        </svg>
                        Registrar marco
                    </h2>
                    <p class="text-muted mb-0">Captura los datos principales del marco curricular para mantener la oferta académica ordenada.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información general</h3>
                    <div class="card-actions">
                        <small class="text-secondary">Los campos marcados con * son obligatorios</small>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('frameworks.store') }}" id="framework-create-form" autocomplete="off">
                        @csrf
                        @include('framework.form')
                    </form>
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

            const nameInput = document.getElementById('name');
            nameInput?.focus();
        });
    </script>
@endpush
