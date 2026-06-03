@extends('tablar::page')

@section('title', 'Mis Postulaciones')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mis Postulaciones</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Mis Postulaciones</h2>
                    <p class="text-muted">Gestiona tus solicitudes a ideas de proyecto de grado.</p>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('students.projects.approved.index') }}" class="btn btn-primary">
                        Ver Banco de Ideas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Prioridad</th>
                                <th>Idea / Proyecto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($postulations as $postulation)
                                <tr>
                                    <td>
                                        @php
                                            $priority = $postulation->priorities->first()?->priority_order;
                                            $priorityLabels = [
                                                1 => 'badge bg-red-lt', // Alta
                                                2 => 'badge bg-orange-lt', // Media
                                                3 => 'badge bg-blue-lt', // Baja
                                            ];
                                            $priorityTexts = [1 => '1 (Alta)', 2 => '2 (Media)', 3 => '3 (Baja)'];
                                        @endphp
                                        <span class="{{ $priorityLabels[$priority] ?? 'badge bg-secondary-lt' }}">
                                            {{ $priorityTexts[$priority] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-medium">
                                            <a href="{{ route('students.postulations.show', $postulation) }}" class="text-decoration-none">
                                                {{ $postulation->project->title }}
                                            </a>
                                        </div>
                                        <div class="text-muted">{{ $postulation->project->thematicArea->name ?? 'Sin área' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pendiente',
                                                'approved' => 'Aprobada',
                                                'rejected' => 'Rechazada',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$postulation->status] ?? 'bg-secondary' }} text-white">
                                            {{ $statusLabels[$postulation->status] ?? $postulation->status }}
                                        </span>
                                    </td>
                                    <td>{{ $postulation->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if ($postulation->status === 'pending' && $postulation->lead_student_id === $student->id)
                                                <form action="{{ route('students.postulations.destroy', $postulation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta postulación?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cancelar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aún no has realizado ninguna postulación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
