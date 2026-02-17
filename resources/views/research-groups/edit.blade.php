{{--
    View path: research-groups/edit.blade.php.
    Purpose: Renders the edit.blade view for the Research Groups module.
    Expected variables within this template: $researchGroup.
    Included partials or components: research-groups.form, tablar::common.alert.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Editar grupo de investigación')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('research-groups.index') }}">Grupos de investigación</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12h6" />
                            <path d="M12 9v6" />
                            <rect x="5" y="5" width="14" height="14" rx="2" />
                        </svg>
                        Editar grupo: {{ $researchGroup->name }}
                    </h2>
                    <p class="text-muted mb-0">Actualiza la información del grupo de investigación seleccionado.</p>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <a href="{{ route('research-groups.show', $researchGroup) }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 7h14" />
                            <path d="M5 12h14" />
                            <path d="M5 17h14" />
                        </svg>
                        Ver detalle
                    </a>
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
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos generales</h3>
                            <div class="card-actions">
                                <span class="badge bg-indigo-lt">ID {{ $researchGroup->id }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Form element sends the captured data to the specified endpoint. --}}
                            <form method="POST" action="{{ route('research-groups.update', $researchGroup) }}">
                                @csrf
                                @method('PUT')
                                @include('research-groups.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
