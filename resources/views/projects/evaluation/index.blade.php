{{-- 
    View path: projects/evaluation/index.blade.php
    Purpose: Allows committee leaders to view and evaluate projects from their program.
--}}
@extends('tablar::page')

@section('title', 'Evaluación de proyectos')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Evaluación de proyectos</li>
                    </ol>
                </nav>
                <h2 class="page-title d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-indigo" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="4" width="16" height="16" rx="2" />
                        <path d="M8 12h8M8 8h8M8 16h8" />
                    </svg>
                    Proyectos pendientes
                    <span class="badge bg-indigo ms-2">{{ $projects->count() }}</span>
                </h2>
                <p class="text-muted mb-0">Evalúa los proyectos asociados a tu programa académico.</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Área temática</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Autores</th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $index => $project)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="font-medium text-wrap">{{ $project->title }}</td>
                                <td>{{ $project->thematicArea->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-warning-lt">{{ $project->projectStatus->name }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($project->date)->format('d/m/Y') }}</td>
                                <td>
                                    @foreach($project->students as $s)
                                        <span class="badge bg-blue-lt">{{ $s->name }} {{ $s->last_name }}</span>
                                    @endforeach
                                    @foreach($project->professors as $p)
                                        <span class="badge bg-green-lt">{{ $p->name }} {{ $p->last_name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('projects.evaluation.show', $project) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                       Ver detalles
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <rect x="4" y="4" width="16" height="16" rx="2" />
                                                <path d="M8 12h8M8 8h8M8 16h8" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay proyectos pendientes</p>
                                        <p class="empty-subtitle text-muted">Aún no hay proyectos para evaluar en tu programa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
