@extends('tablar::page')

@section('title', 'Evaluación de Postulaciones')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Evaluación de Postulaciones</h2>
                    <p class="text-muted">Revisa y decide sobre las solicitudes de los estudiantes.</p>
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
                                <th>Proyecto</th>
                                <th>Líder / Equipo</th>
                                <th>Fecha</th>
                                <th class="w-1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($postulations as $postulation)
                                <tr>
                                    <td>
                                        <div class="font-weight-medium">{{ $postulation->project->title }}</div>
                                        <div class="text-muted small">{{ $postulation->project->thematicArea->name }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $postulation->leadStudent->name }} {{ $postulation->leadStudent->last_name }}</div>
                                        <div class="text-muted small">
                                            @php $memberCount = $postulation->members->count(); @endphp
                                            {{ $memberCount > 1 ? ($memberCount . ' integrantes') : 'Individual' }}
                                        </div>
                                    </td>
                                    <td>{{ $postulation->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('projects.evaluation.postulations.show', $postulation) }}" class="btn btn-primary btn-sm">Evaluar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay postulaciones pendientes de evaluación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
