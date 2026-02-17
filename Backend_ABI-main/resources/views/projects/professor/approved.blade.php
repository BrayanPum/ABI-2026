{{-- 
    View path: projects/student/approved.blade.php
    Purpose: Allows students to view the approved projects in their program or where they participate.
--}}

@extends('tablar::page')

@section('title', 'Proyectos Aprobados')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Proyectos aprobados</li>
                    </ol>
                </nav>
                <h2 class="page-title d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    Proyectos aprobados
                    <span class="badge bg-success ms-2">{{ $projects->count() }}</span>
                </h2>
                <p class="text-muted mb-0">Consulta los proyectos aprobados de tu programa académico o en los que participas.</p>
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
                                <td class="fw-medium text-wrap">{{ $project->title }}</td>
                                <td>{{ $project->thematicArea->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success-lt">{{ $project->projectStatus->name }}</span>
                                </td>
                                <td>
                                    {{ $project->created_at ? $project->created_at->format('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    @foreach($project->students as $s)
                                        <span class="badge bg-blue-lt">{{ $s->name }} {{ $s->last_name }}</span>
                                    @endforeach
                                    @foreach($project->professors as $p)
                                        <span class="badge bg-green-lt">{{ $p->name }} {{ $p->last_name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('professor.projects.approved.show', $project) }}" 
                                       class="btn btn-sm btn-outline-success">
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
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        </div>
                                        <p class="empty-title">No hay proyectos aprobados</p>
                                        <p class="empty-subtitle text-muted">Aún no hay proyectos aprobados disponibles para tu programa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($projects->hasPages())
                    <div class="card-footer">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                            {{-- Muestra el rango y el total de proyectos --}}
                            <p class="m-0 text-muted">
                                Mostrando {{ $projects->firstItem() }}–{{ $projects->lastItem() }} de {{ $projects->total() }} resultados
                            </p>
                            
                            {{-- Controles de paginación (Bootstrap 5) --}}
                            {{ $projects->withQueryString()->links('vendor.pagination.bootstrap-5-numeric') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
