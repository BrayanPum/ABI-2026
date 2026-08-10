@extends('tablar::page')

@section('title', 'Seleccionar Idea Aprobada')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('students.projects.approved.index') }}">Ideas Aprobadas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Seleccionar Idea</li>
                    </ol>
                </nav>
                <h2 class="page-title mb-0">
                    Seleccionar Idea Aprobada
                </h2>
                <p class="text-muted">Asume esta idea y arma tu equipo antes de ejecutarla.</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="alert alert-info mb-3">
            <strong>Ventana de seleccion:</strong>
            {{ optional($activeAcademicPeriod)->name ?? 'Periodo activo' }} ·
            {{ optional($selectionWindow->start_at)->format('d/m/Y H:i') }} a {{ optional($selectionWindow->end_at)->format('d/m/Y H:i') }}.
        </div>

        @if(isset($selectionWindow) && $selectionWindow->requires_evaluation)
            <div class="alert alert-warning">
                <h4 class="alert-title">¡Atención! Proceso de Postulación Activo</h4>
                <div class="text-secondary">
                    Para este periodo, no se permite la asignación directa. Debes enviar una postulación para que sea revisada por un evaluador.
                </div>
                <div class="mt-3">
                    <a href="{{ route('students.postulations.create', $project) }}" class="btn btn-warning w-100">
                        Ir al formulario de Postulación
                    </a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('projects.student.assign', $project) }}">
                @csrf
                {{-- ... resto del formulario --}}

            <div class="row g-3">
                <div class="col-12 col-lg-8">

                    {{-- Resumen del Proyecto --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1">{{ $project->title }}</h3>
                                <small class="text-secondary">
                                    Área: {{ $project->thematicArea->name ?? 'No definida' }} •
                                    Línea: {{ $project->thematicArea->investigationLine->name ?? 'No definida' }}
                                </small>
                            </div>
                            <span class="badge bg-indigo">Aprobado</span>
                        </div>
                        <div class="card-body">
                            <p class="text-secondary mb-0">
                                Estás a punto de asumir este proyecto. Si continúas, el estado cambiará a
                                <span class="fw-semibold text-dark">Asignado</span> y pasará a tu responsabilidad.
                            </p>
                        </div>
                    </div>

                    {{-- Selección de compañeros --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Arma tu equipo (opcional)</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                Puedes seleccionar hasta <strong>2 compañeros</strong> de tu mismo programa.  
                                Ellos se unirán a ti en la ejecución del proyecto.
                            </p>

                            {{-- Campo de búsqueda --}}
                            <input type="text" id="student-search" class="form-control mb-2" placeholder="Buscar por nombre o documento...">

                            {{-- Lista filtrable --}}
                            <div id="student-list" class="list-group" style="max-height: 220px; overflow-y: auto;">
                                @foreach ($availableStudents as $s)
                                    <button type="button"
                                        class="list-group-item list-group-item-action student-option"
                                        data-id="{{ $s->id }}"
                                        data-name="{{ $s->name }} {{ $s->last_name }}"
                                        data-card="{{ $s->card_id }}">
                                        {{ $s->name }} {{ $s->last_name }} — {{ $s->card_id }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Seleccionados --}}
                            <div id="selected-students" class="mt-3"></div>

                            {{-- Hidden inputs --}}
                            <div id="selected-students-inputs"></div>

                            <small class="form-hint mt-2 d-block">
                                Si no deseas compañeros, puedes asignarte solo.
                            </small>

                            @error('teammate_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('students.projects.approved.index') }}" class="btn btn-outline-secondary w-50">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary w-50">
                            Confirmar Selección
                        </button>
                    </div>
                </div>

                {{-- Info Lateral --}}
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Información Clave</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small text-secondary mb-0">
                                <li>• El proyecto pasará a estado <strong>Asignado</strong></li>
                                <li>• Serás responsable de su ejecución</li>
                                <li>• Podrás verlo luego en tu listado de proyectos</li>
                                <li>• Podrás trabajar con 0, 1 o 2 compañeros</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        @endif

    </div>
</div>
@endsection

@push('css')
<style>
    #selected-students .chip {
        display: inline-flex;
        align-items: center;
        background-color: var(--tblr-primary-lt);
        color: var(--tblr-primary);
        padding: 4px 10px;
        border-radius: 50px;
        margin: 4px;
        font-size: 0.875rem;
    }
    #selected-students .chip button {
        background: none;
        border: none;
        margin-left: 6px;
        cursor: pointer;
        color: inherit;
    }
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('student-search');
    const studentList = document.getElementById('student-list');
    const selectedContainer = document.getElementById('selected-students');
    const hiddenInputs = document.getElementById('selected-students-inputs');

    let selected = [];

    // 🔍 Filtrar estudiantes
    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        document.querySelectorAll('.student-option').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term) ? '' : 'none';
        });
    });

    // ✅ Seleccionar estudiante
    studentList.addEventListener('click', function (e) {
        const button = e.target.closest('.student-option');
        if (!button) return;

        const id = String(button.dataset.id);
        const name = button.dataset.name;
        const card = button.dataset.card;

        if (selected.length >= 2) {
            alert("Solo puedes seleccionar hasta 2 compañeros.");
            return;
        }

        if (selected.some(s => s.id === id)) {
            return; // Ya seleccionado
        }

        selected.push({ id, name, card });

        renderSelected();
    });

    // ❌ Eliminar estudiante
    selectedContainer.addEventListener('click', function (e) {
        if (!e.target.classList.contains('remove-student')) return;

        const id = e.target.dataset.id;
        selected = selected.filter(s => s.id !== id);

        renderSelected();
    });

    // 🎨 Render chips + hidden inputs
    function renderSelected() {
        selectedContainer.innerHTML = '';
        hiddenInputs.innerHTML = '';

        selected.forEach(s => {
            const chip = document.createElement('div');
            chip.className = "d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-body-secondary text-body";
            chip.innerHTML = `
                <span>${s.name} - ${s.card}</span>
                <button type="button" class="btn btn-sm btn-danger remove-student" data-id="${s.id}">X</button>
            `;
            selectedContainer.appendChild(chip);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'teammate_ids[]';
            input.value = s.id;
            hiddenInputs.appendChild(input);
        });
    }
});

</script>
