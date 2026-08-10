@extends('tablar::page')

@section('title', 'Detalle de mi Postulación')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('students.postulations.index') }}">Mis Postulaciones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                        </ol>
                    </nav>
                    <h2 class="page-title">Detalle de mi Postulación</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('students.postulations.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Justificación e Interés</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-justify" style="white-space: pre-wrap;">{{ $postulation->justification }}</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Integrantes del Equipo</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Carné</th>
                                        <th>Rol</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($postulation->members as $member)
                                        <tr>
                                            <td>
                                                {{ $member->student->full_name }}
                                                @if ($member->is_lead) <span class="badge bg-blue-lt">Líder</span> @endif
                                                @if ($member->student_id === $student->id) <span class="badge bg-green-lt">Tú</span> @endif
                                            </td>
                                            <td>{{ $member->student->card_id }}</td>
                                            <td>{{ $member->role_description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($postulation->review_comment)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Observaciones del Comité</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted italic">"{{ $postulation->review_comment }}"</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Información del Proyecto</h3>
                        </div>
                        <div class="card-body">
                            <h4>{{ $postulation->project->title }}</h4>
                            <p class="text-muted small">{{ $postulation->project->thematicArea->name }}</p>
                            <hr>
                            <div class="mb-2">
                                <strong>Estado:</strong>
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
                            </div>
                            <div class="mb-2">
                                <strong>Prioridad asignada:</strong>
                                @php
                                    $priority = $postulation->priorities->first()?->priority_order;
                                    $priorityTexts = [1 => '1 (Alta)', 2 => '2 (Media)', 3 => '3 (Baja)'];
                                @endphp
                                <span class="text-indigo fw-bold">{{ $priorityTexts[$priority] ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Fecha de envío:</strong>
                                <span class="text-muted small">{{ $postulation->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Docente Proponente</h3>
                        </div>
                        <div class="card-body">
                            @foreach($postulation->project->professors as $professor)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="avatar bg-azure-lt text-primary me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                    <div>
                                        <div class="fw-bold">{{ $professor->name }} {{ $professor->last_name }}</div>
                                        <div class="text-muted small">{{ $professor->mail ?? $professor->user?->email }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
