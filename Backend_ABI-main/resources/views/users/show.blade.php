{{--
    View path: users/show.blade.php.
    Purpose: Renders the show.blade view for the Users module.
    Expected variables within this template: $details, $user.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Detalle del usuario')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <circle cx="12" cy="7" r="4" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        {{ $details->name ?? $user->name ?? 'Usuario sin nombre' }} {{ $details->last_name ?? $user->last_name ?? '' }}
                    </h2>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            Editar usuario
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información de cuenta</h3>
                            <div class="card-actions text-muted small">
                                Actualizado {{ $user->updated_at?->diffForHumans() ?? 'sin cambios' }}
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Email</dt>
                                <dd class="col-sm-9">{{ $user->email }}</dd>

                                <dt class="col-sm-3">Rol</dt>
                                <dd class="col-sm-9">
                                    @switch($user->role)
                                        @case('student')
                                            <span class="badge bg-info-lt">Estudiante</span>
                                            @break
                                        @case('professor')
                                            <span class="badge bg-primary-lt">Profesor</span>
                                            @break
                                        @case('committee_leader')
                                            <span class="badge bg-warning-lt">Líder de Comité</span>
                                            @break
                                        @case('research_staff')
                                            <span class="badge bg-success-lt">Staff de Investigación</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary-lt">{{ $user->role }}</span>
                                    @endswitch
                                </dd>

                                <dt class="col-sm-3">Estado</dt>
                                <dd class="col-sm-9">
                                    @if($user->state === 1)
                                        <span class="badge bg-success-lt">Activo</span>
                                    @else
                                        <span class="badge bg-danger-lt">Inactivo</span>
                                    @endif
                                </dd>
                                
                                @if($details)
                                    <dt class="col-sm-3">Cédula</dt>
                                    <dd class="col-sm-9">{{ $details->card_id }}</dd>
                                    
                                    <dt class="col-sm-3">Teléfono</dt>
                                    <dd class="col-sm-9">{{ $details->phone }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                @if($details)
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Información personal</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nombre</dt>
                                    <dd class="col-sm-8">{{ $details->name }}</dd>
                                    
                                    <dt class="col-sm-4">Apellido</dt>
                                    <dd class="col-sm-8">{{ $details->last_name }}</dd>
                                    
                                    @if($user->role === 'student')
                                        <dt class="col-sm-4">Semestre</dt>
                                        <dd class="col-sm-8">{{ $details->semester }}</dd>
                                    @elseif(in_array($user->role, ['professor', 'committee_leader']))
                                        <dt class="col-sm-4">Líder de Comité</dt>
                                        <dd class="col-sm-8">
                                            @if($details->committee_leader)
                                                <span class="badge bg-success-lt">Sí</span>
                                            @else
                                                <span class="badge bg-secondary-lt">No</span>
                                            @endif
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    @if(in_array($user->role, ['student', 'professor', 'committee_leader']) && $details->cityProgram)
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title">Programa académico</h3>
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Programa</dt>
                                        <dd class="col-sm-8">{{ $details->cityProgram->program->name }}</dd>
                                        
                                        <dt class="col-sm-4">Ciudad</dt>
                                        <dd class="col-sm-8">{{ $details->cityProgram->city->name }}</dd>
                                        
                                        <dt class="col-sm-4">Código</dt>
                                        <dd class="col-sm-8">{{ $details->cityProgram->program->code ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
