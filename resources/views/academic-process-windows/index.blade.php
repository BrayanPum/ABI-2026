@extends('tablar::page')
@section('title', 'Calendario academico')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Calendario academico</h2>
                <p class="text-muted mb-0">Administra las fechas limite de cada proceso dentro de cada periodo.</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('academic-periods.index') }}" class="btn btn-outline-secondary">Ver periodos</a>
                    <a href="{{ route('academic-process-windows.create') }}" class="btn btn-primary">Nueva ventana</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(config('tablar.display_alert'))
            @include('tablar::common.alert')
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Periodo academico</label>
                        <select name="academic_period_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($periods as $id => $periodName)
                                <option value="{{ $id }}" {{ (string) $academicPeriodId === (string) $id ? 'selected' : '' }}>{{ $periodName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Proceso</label>
                        <select name="process_key" class="form-select">
                            <option value="">Todos</option>
                            @foreach($processOptions as $value => $label)
                                <option value="{{ $value }}" {{ (string) $processKey === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="scheduled" {{ (string) $status === 'scheduled' ? 'selected' : '' }}>Programadas</option>
                            <option value="active" {{ (string) $status === 'active' ? 'selected' : '' }}>Activas</option>
                            <option value="closed" {{ (string) $status === 'closed' ? 'selected' : '' }}>Cerradas</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label class="form-label">Registros</label>
                        <select name="per_page" class="form-select">
                            @foreach([10,25,50] as $size)
                                <option value="{{ $size }}" {{ (int) $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-1 d-grid">
                        <button class="btn btn-outline-primary">Ir</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Periodo</th>
                            <th>Proceso</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th class="text-center">Req. Eval.</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($windows as $window)
                            <tr>
                                <td>{{ $window->academicPeriod?->name ?? 'N/D' }}</td>
                                <td>{{ $processOptions[$window->process_key] ?? $window->process_key }}</td>
                                <td>{{ $window->start_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $window->end_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    @if($window->requires_evaluation)
                                        <span class="badge bg-purple text-purple-fg">Sí</span>
                                    @else
                                        <span class="badge bg-secondary text-secondary-fg">No</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($window->calculated_status_key) {
                                            'active' => 'bg-success-lt text-success',
                                            'scheduled' => 'bg-yellow-lt text-yellow',
                                            'closed' => 'bg-secondary-lt text-secondary',
                                            default => 'bg-muted-lt text-muted',
                                        };
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">{{ $window->calculated_status }}</span>
                                </td>
                                <td>
                                    <div class="btn-list justify-content-center flex-nowrap">
                                        <a href="{{ route('academic-process-windows.show', $window) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        <a href="{{ route('academic-process-windows.edit', $window) }}" class="btn btn-sm btn-outline-success">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty">
                                        <p class="empty-title">No hay ventanas configuradas</p>
                                        <p class="empty-subtitle text-muted">Crea las fechas de propuesta y seleccion para controlar el flujo por periodo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($windows->hasPages())
                <div class="card-footer">{{ $windows->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
