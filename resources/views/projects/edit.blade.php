{{-- 
    View path: projects/edit.blade.php.
    Purpose: Allows professors and students to update their project idea while generating a new version.
--}}
@extends('tablar::page')

@section('title', 'Editar proyecto')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                        </svg>
                        Editar proyecto #{{ $project->id }}
                    </h2>
                    <p class="text-muted mb-0">Actualiza la propuesta manteniendo el historial de versiones.</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
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

            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos generales</h3>
                            <div class="card-actions">
                                <span class="badge bg-success">ID {{ $project->id }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('projects.update', $project) }}" autocomplete="off" novalidate>
                                @csrf
                                @method('PUT')
                                @include('projects.form', [
                                    'project' => $project,
                                    'prefill' => $prefill ?? [],
                                    'contentValues' => $contentValues ?? [],
                                    'isProfessor' => $isProfessor ?? false,
                                    'isStudent' => $isStudent ?? false,
                                    'cities' => $cities ?? collect(),
                                    'programs' => $programs ?? collect(),
                                    'investigationLines' => $investigationLines ?? collect(),
                                    'thematicAreas' => $thematicAreas ?? collect(),
                                    'availableStudents' => $availableStudents ?? collect(),
                                    'availableProfessors' => $availableProfessors ?? collect(),
                                ])

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('projects.index') }}" class="btn btn-link">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @php
        // ✅ Prepare data for JSON safely to avoid Blade parse errors
        $areasForJs = ($thematicAreas ?? collect())
            ->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'investigation_line_id' => $area->investigation_line_id,
                ];
            })
            ->values();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const investigationSelect = document.getElementById('investigation_line_id');
            const thematicSelect = document.getElementById('thematic_area_id');

            // ✅ Use the precomputed PHP variable instead of mapping again
            const areas = @json($areasForJs);

            function renderAreas(lineId) {
                thematicSelect.innerHTML = '<option value="">Selecciona un área temática</option>';
                areas.forEach(area => {
                    if (!lineId || Number(lineId) === Number(area.investigation_line_id)) {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = area.name;
                        if (String(area.id) === thematicSelect.dataset.selected) {
                            option.selected = true;
                        }
                        thematicSelect.appendChild(option);
                    }
                });
            }

            if (thematicSelect) {
                thematicSelect.dataset.selected = thematicSelect.value;
            }

            if (investigationSelect) {
                investigationSelect.addEventListener('change', event => {
                    thematicSelect.dataset.selected = '';
                    renderAreas(event.target.value);
                });

                renderAreas(investigationSelect.value);
            }
        });
    </script>
@endpush
