{{-- 
    Partial path: projects/form.blade.php.
    Purpose: Shared form fields for create and edit operations.
--}}
@php
    $projectModel = $project ?? null;
    $contentValues = $contentValues ?? [];
    $prefill = $prefill ?? [];
    $cities = $cities ?? collect();
    $programs = $programs ?? collect();
    $investigationLines = $investigationLines ?? collect();
    $thematicAreas = $thematicAreas ?? collect();
    $availableStudents = $availableStudents ?? collect();
    $availableProfessors = collect($availableProfessors ?? []); // Coerce the initial participants into a collection so helper methods like ->values() are available consistently.
    $initialProfessorOptions = $availableProfessors->values(); // Cache a zero-indexed copy to feed the data attribute used by JavaScript.
    $isEdit = $isEdit ?? false;
@endphp

@if (!empty($versionComment))
    <div class="card border-danger mb-3">
        <div class="card-header bg-danger text-white">
            Comentarios del revisor
        </div>
        <div class="card-body">
            <div class="p-2 border-start border-3 border-danger rounded">
                {!! nl2br(e($versionComment)) !!}
            </div>
            <small class="text-muted d-block mt-2">
                *Corrige tu propuesta según estos comentarios antes de reenviar.
            </small>
        </div>
    </div>
@endif

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label">Fecha de entrega</label>
        <input type="text" class="form-control" value="{{ $prefill['delivery_date'] ?? now()->format('Y-m-d') }}" readonly>
        <small class="form-hint">Se registra automáticamente con la fecha actual.</small>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Ciudad</label>
        <input type="text"
               class="form-control"
               value="{{ $cities->firstWhere('id', $prefill['city_id'])->name ?? 'Ciudad no disponible' }}"
               readonly>
        <input type="hidden" name="city_id" value="{{ $prefill['city_id'] }}">
        <small class="form-hint">Asignada automáticamente según tu usuario.</small>
    </div>

</div>


@if ($isProfessor)
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="program_id" class="form-label required">Programa académico</label>
            @php
                // Resolve the program name using the prefilled identifier so the field can stay read-only.
                $lockedProgramId = old('program_id', $prefill['program_id'] ?? null);
                $lockedProgramName = optional($programs->firstWhere('id', $lockedProgramId))->name ?? 'Programa no disponible';
            @endphp
            <input type="text" id="program_id_display" class="form-control" value="{{ $lockedProgramName }}" readonly disabled>
            <input type="hidden" id="program_id" name="program_id" value="{{ $lockedProgramId }}">
            <small class="form-hint">El programa se fija según tu perfil y no puede modificarse.</small>
            @error('program_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label">Profesores asociados</label>
            @php
                // Gather the authenticated professor to exclude them from the dynamic chips component.
                $fullUser = \App\Helpers\AuthUserHelper::fullUser();
                $currentProfessorId = optional($fullUser?->professor)->id;
                $initialProfessorIds = collect(old('associated_professors', $projectModel?->professors?->pluck('id')->reject(fn ($id) => $id === $currentProfessorId)->all() ?? []))
                    ->filter(static fn ($id) => $id !== null)
                    ->unique()
                    ->values();
                $initialProfessorData = $projectModel?->professors
                    ? $projectModel->professors
                        ->whereIn('id', $initialProfessorIds)
                        ->map(static function ($professorItem) {
                            return [
                                'id' => $professorItem->id,
                                'name' => trim($professorItem->name . ' ' . $professorItem->last_name),
                                'document' => $professorItem->card_id,
                            ];
                        })
                        ->values()
                    : collect();

                $professorPrograms = $availableProfessors
                    ->map(static function ($option) {
                        $programName = $option['program'] ?? null;
                        $cityName = $option['program_city'] ?? null;
                        $programId = $option['program_id'] ?? null;

                        if (empty($programName) || empty($programId)) {
                            return null;
                        }

                        $displayName = $programName;
                        if (! empty($cityName)) {
                            $displayName = sprintf('%s — %s', $programName, $cityName);
                        }

                        return [
                            'id' => $programId,
                            'name' => $displayName,
                        ];
                    })
                    ->filter()
                    ->unique(static fn ($option) => $option['id'])
                    ->sortBy('name')
                    ->values();
            @endphp

            {{-- Picker simplificado de profesores --}}
            <div class="card card-sm shadow-none border border-dashed professor-picker-card" data-professor-search data-initial-professors='@json($initialProfessorData)'>
                <div class="card-header py-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="card-title mb-0">Docentes disponibles</span>
                        <span class="badge bg-secondary" data-professor-available-count>{{ $availableProfessors->count() }}</span>
                    </div>
                </div>

                <div class="card-body pb-2">
                    <div class="row g-2">
                        <div class="col-12 col-md">
                            <label for="professor-search-input" class="form-label text-secondary mb-1">Buscar docente</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="10" cy="10" r="7" />
                                        <line x1="21" y1="21" x2="15" y2="15" />
                                    </svg>
                                </span>
                                <input type="text" id="professor-search-input" class="form-control" placeholder="Nombre, documento o correo" data-professor-search-input>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto">
                            <label for="professor-program-filter" class="form-label text-secondary mb-1">Programa académico</label>
                            <select id="professor-program-filter" class="form-select" data-professor-program-filter>
                                <option value="">Todos los programas</option>
                                @foreach ($professorPrograms as $programOption)
                                    <option value="{{ $programOption['id'] }}">{{ $programOption['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="px-3 py-2 text-secondary small d-none" data-professor-empty-list data-empty-default="No hay docentes disponibles." data-empty-filter="No hay docentes que coincidan con la búsqueda."></div>
                </div>

                {{-- listado compacto con scroll interno --}}
                <div class="list-group list-group-flush border-top professor-picker-list" data-professor-initial-list>
                    @forelse ($availableProfessors as $option)
                        <button type="button"
                                class="list-group-item list-group-item-action text-start"
                                data-professor-option="{{ $option['id'] }}"
                                data-professor-option-name="{{ $option['name'] }}"
                                data-professor-option-document="{{ $option['document'] }}"
                                data-professor-option-email="{{ $option['email'] }}"
                                data-professor-option-program="{{ $option['program_id'] ?? '' }}"
                                data-professor-option-program-name="{{ $option['program'] ?? '' }}"
                                data-professor-option-program-city="{{ $option['program_city'] ?? '' }}">
                            <span class="fw-semibold d-block">{{ $option['name'] }}</span>
                            <span class="text-secondary small d-block">{{ $option['document'] ?? 'Sin documento' }}</span>
                            @if(!empty($option['email']))
                                <span class="text-secondary small d-block">{{ $option['email'] }}</span>
                            @endif
                            @if(!empty($option['program']))
                                <span class="text-secondary small d-block">Programa: {{ $option['program'] }}@if(!empty($option['program_city'])) — {{ $option['program_city'] }}@endif</span>
                            @endif
                        </button>
                    @empty
                        <div class="text-secondary small px-3 py-2">No hay participantes disponibles.</div>
                    @endforelse
                </div>

                <div class="px-3 py-2 text-secondary small d-none border-top" data-professor-empty-list data-empty-default="No hay docentes disponibles." data-empty-filter="No hay docentes que coincidan con la búsqueda."></div>

                {{-- chips de seleccionados --}}
                <div class="card-footer bg-white border-top py-2">
                    <div class="d-flex flex-wrap gap-2" data-professor-selected>
                        <span class="text-secondary small" data-professor-empty-hint>Sin profesores asociados todavía.</span>
                    </div>
                </div>
            </div>
            <small class="form-hint">Haz clic en un profesor para agregarlo. Retira desde la ficha ×.</small>

            @error('associated_professors')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @error('associated_professors.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
@else
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label class="form-label">Programa académico</label>
            <input type="text" class="form-control" value="{{ $programs->firstWhere('id', $prefill['program_id'] ?? null)->name ?? 'No asignado' }}" readonly>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Grupo de investigación</label>
            <input type="text" class="form-control" value="{{ $prefill['research_group'] ?? 'No asignado' }}" readonly>
        </div>
    </div>
@endif

<div class="row g-3 mt-0">
    <div class="col-12 col-md-6">
        <label for="investigation_line_id" class="form-label required">Línea de investigación</label>
        <select id="investigation_line_id" name="investigation_line_id"
                class="form-select @error('investigation_line_id') is-invalid @enderror" required>
            <option value="">Selecciona una línea</option>
            @foreach ($investigationLines as $line)
                <option value="{{ $line->id }}"
                    {{ (string) old('investigation_line_id', $projectModel?->thematicArea?->investigation_line_id ?? '') === (string) $line->id ? 'selected' : '' }}>
                    {{ $line->name }}
                </option>
            @endforeach
        </select>
        @error('investigation_line_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="thematic_area_id" class="form-label required">Área temática</label>
        <select id="thematic_area_id" name="thematic_area_id"
                class="form-select @error('thematic_area_id') is-invalid @enderror" required disabled>
            <option value="">Selecciona un área temática</option>
            @foreach ($thematicAreas as $area)
                <option value="{{ $area->id }}"
                        data-line="{{ $area->investigation_line_id }}"
                        {{ (string) old('thematic_area_id', $projectModel?->thematic_area_id ?? '') === (string) $area->id ? 'selected' : '' }}>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
        <small class="form-hint">Selecciona primero una linea de investigación.</small>
        @error('thematic_area_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


@once
    @push('css')
        <style>
            .professor-picker-card {
                display: flex;
                flex-direction: column;
                gap: 0;
                max-height: 100%;
            }

            .professor-picker-list {
                flex: 1 1 auto;
                max-height: 260px;
                overflow-y: auto;
            }

            @media (max-width: 767.98px) {
                .professor-picker-list {
                    max-height: 320px;
                }
            }
        </style>
    @endpush
@endonce

@once
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-professor-search]').forEach(container => {
                    // Elementos principales (solo los necesarios)
                    const elements = {
                        initialList: container.querySelector('[data-professor-initial-list]'),
                        selectedWrapper: container.querySelector('[data-professor-selected]'),
                        emptyHint: container.querySelector('[data-professor-empty-hint]'),
                        countBadge: container.querySelector('[data-professor-available-count]'),
                        searchInput: container.querySelector('[data-professor-search-input]'),
                        programFilter: container.querySelector('[data-professor-program-filter]'),
                        emptyList: container.querySelector('[data-professor-empty-list]'),
                    };

                    const selectedMap = new Map();

                    // Utilidades
                    const normalizeProfessor = (raw) => ({
                        id: Number.parseInt(raw?.id ?? raw, 10),
                        name: raw?.name ?? '',
                        document: raw?.document ?? '',
                        email: raw?.email ?? '',
                    });

                    const toggleEmptyHint = () => {
                        elements.emptyHint?.classList.toggle('d-none', selectedMap.size > 0);
                    };

                    const applyFilters = () => {
                        const searchValue = elements.searchInput?.value ?? '';
                        const term = searchValue.toLowerCase().trim();
                        const programId = elements.programFilter?.value ?? '';
                        let visibleCount = 0;

                        const options = elements.initialList?.querySelectorAll('[data-professor-option]') ?? [];

                        options.forEach(option => {
                            const optionName = option.dataset.professorOptionName?.toLowerCase() ?? '';
                            const optionDocument = option.dataset.professorOptionDocument?.toLowerCase() ?? '';
                            const optionEmail = option.dataset.professorOptionEmail?.toLowerCase() ?? '';
                            const optionProgram = option.dataset.professorOptionProgram ?? '';

                            const matchesTerm = term === '' || optionName.includes(term) || optionDocument.includes(term) || optionEmail.includes(term);
                            const matchesProgram = programId === '' || optionProgram === programId;
                            const shouldShow = matchesTerm && matchesProgram;

                            option.classList.toggle('d-none', !shouldShow);
                            option.tabIndex = shouldShow ? 0 : -1;

                            if (shouldShow) {
                                visibleCount += 1;
                            }
                        });

                        if (elements.countBadge) {
                            elements.countBadge.textContent = String(visibleCount);
                        }

                        if (elements.emptyList) {
                            const hasOptions = (elements.initialList?.querySelector('[data-professor-option]') ?? null) !== null;
                            if (!hasOptions) {
                                elements.emptyList.textContent = elements.emptyList.dataset.emptyDefault ?? '';
                                elements.emptyList.classList.remove('d-none');
                            } else if (visibleCount === 0) {
                                elements.emptyList.textContent = elements.emptyList.dataset.emptyFilter ?? '';
                                elements.emptyList.classList.remove('d-none');
                            } else {
                                elements.emptyList.classList.add('d-none');
                            }
                        }
                    };

                    const removeOptionFromList = (id) => {
                        elements.initialList?.querySelector(`[data-professor-option="${id}"]`)?.remove();
                        applyFilters();
                    };

                    // Crear chip de profesor seleccionado
                    const createChip = (professor) => {
                        const chip = document.createElement('span');
                        chip.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-2';
                        chip.dataset.professorChip = professor.id;

                        chip.innerHTML = `
                            <span class="fw-semibold">${professor.name}</span>
                            <span class="badge bg-white text-primary">${professor.document || 'Sin documento'}</span>
                            <button type="button" class="btn-close btn-close-white" aria-label="Eliminar profesor asociado"></button>
                            <input type="hidden" name="associated_professors[]" value="${professor.id}">
                        `;

                        chip.querySelector('button').addEventListener('click', () => {
                            chip.remove();
                            selectedMap.delete(professor.id);
                            toggleEmptyHint();
                        });

                        return chip;
                    };

                    // Agregar profesor
                    const addProfessor = (professor) => {
                        if (!professor?.id || selectedMap.has(professor.id)) return;

                        selectedMap.set(professor.id, professor);
                        elements.selectedWrapper?.appendChild(createChip(professor));
                        removeOptionFromList(professor.id);
                        toggleEmptyHint();
                    };

                    // Event handler para clicks en opciones de la lista inicial
                    const handleOptionClick = (event) => {
                        const target = event.target.closest('[data-professor-option]');
                        if (!target) return;

                        const professor = normalizeProfessor({
                            id: target.dataset.professorOption,
                            name: target.dataset.professorOptionName,
                            document: target.dataset.professorOptionDocument,
                            email: target.dataset.professorOptionEmail,
                        });

                        addProfessor(professor);
                    };

                    // Inicialización de event listeners
                    elements.initialList?.addEventListener('click', handleOptionClick);
                    elements.searchInput?.addEventListener('input', applyFilters);
                    elements.programFilter?.addEventListener('change', applyFilters);

                    // Cargar datos iniciales
                    try {
                        const initialProfessors = JSON.parse(container.dataset.initialProfessors || '[]');
                        initialProfessors.forEach(prof => addProfessor(normalizeProfessor(prof)));
                    } catch (error) {
                        console.warn('Error parsing initial professors', error);
                    }

                    toggleEmptyHint();
                    applyFilters();
                });
            });
        </script>
    @endpush
@endonce

<div class="mb-3 mt-3">
    <label for="title" class="form-label required">Título del proyecto</label>
    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $projectModel->title ?? '') }}" maxlength="255" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if ($isProfessor)
    <div class="mb-3">
        <label for="evaluation_criteria" class="form-label required">Criterios de evaluación</label>
        <textarea id="evaluation_criteria" name="evaluation_criteria" class="form-control @error('evaluation_criteria') is-invalid @enderror" rows="3" required>{{ old('evaluation_criteria', $projectModel->evaluation_criteria ?? '') }}</textarea>
        @error('evaluation_criteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endif

@if ($isProfessor)
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <label for="students_count" class="form-label required">Cantidad de estudiantes</label>
            <input type="number" min="1" max="3" id="students_count" name="students_count" class="form-control @error('students_count') is-invalid @enderror" value="{{ old('students_count', $contentValues['Cantidad de estudiantes'] ?? '') }}" required>
            @error('students_count')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-8">
            <label for="execution_time" class="form-label required">Tiempo de ejecución</label>
            <input type="text" id="execution_time" name="execution_time" class="form-control @error('execution_time') is-invalid @enderror" value="{{ old('execution_time', $contentValues['Tiempo de ejecución'] ?? '') }}" required>
            @error('execution_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label for="viability" class="form-label required">Viabilidad</label>
        <textarea id="viability" name="viability" class="form-control @error('viability') is-invalid @enderror" rows="3" required>{{ old('viability', $contentValues['Viabilidad'] ?? '') }}</textarea>
        @error('viability')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="relevance" class="form-label required">Pertinencia con el grupo y programa</label>
        <textarea id="relevance" name="relevance" class="form-control @error('relevance') is-invalid @enderror" rows="3" required>{{ old('relevance', $contentValues['Pertinencia con el grupo de investigación y con el programa'] ?? '') }}</textarea>
        @error('relevance')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="teacher_availability" class="form-label required">Disponibilidad de docentes</label>
        <textarea id="teacher_availability" name="teacher_availability" class="form-control @error('teacher_availability') is-invalid @enderror" rows="3" required>{{ old('teacher_availability', $contentValues['Disponibilidad de docentes para su dirección y calificación'] ?? '') }}</textarea>
        @error('teacher_availability')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="title_objectives_quality" class="form-label required">Calidad y correspondencia título – objetivos</label>
        <textarea id="title_objectives_quality" name="title_objectives_quality" class="form-control @error('title_objectives_quality') is-invalid @enderror" rows="3" required>{{ old('title_objectives_quality', $contentValues['Calidad y correspondencia entre título y objetivo'] ?? '') }}</textarea>
        @error('title_objectives_quality')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endif

<div class="mb-3">
    <label for="general_objective" class="form-label required">Objetivo general</label>
    <textarea id="general_objective" name="general_objective" class="form-control @error('general_objective') is-invalid @enderror" rows="3" required>{{ old('general_objective', $contentValues['Objetivo general del proyecto'] ?? '') }}</textarea>
    @error('general_objective')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label required">Descripción del proyecto</label>
    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $contentValues['Descripción del proyecto de investigación'] ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if ($isStudent)
<div class="mb-3">
    <label class="form-label">Compañeros asignados</label>

    @if($isEdit)
        {{-- Mostrar seleccionados solamente --}}
        <div id="selected-students">
            @foreach ($availableStudents as $s)
                <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-body-secondary text-body">
                    <span>{{ $s->name }} {{ $s->last_name }} - {{ $s->card_id }}</span>
                </div>
                <input type="hidden" name="teammate_ids[]" value="{{ $s->id }}">
            @endforeach
        </div>

        <small class="form-hint text-muted">No puedes modificar tus compañeros en esta etapa.</small>

    @else
        {{-- CREACIÓN: buscador + lista + selección --}}
        <input type="text" id="student-search" class="form-control mb-2" placeholder="Buscar por nombre o cédula...">

        <div id="student-list" class="list-group" style="max-height: 180px; overflow-y: auto;">
            @foreach ($availableStudents as $s)
                <button type="button"
                    class="list-group-item list-group-item-action student-option"
                    data-id="{{ $s->id }}"
                    data-name="{{ $s->name }} {{ $s->last_name }}"
                    data-card="{{ $s->card_id }}">
                    {{ $s->name }} {{ $s->last_name }} - {{ $s->card_id }}
                </button>
            @endforeach
        </div>

        <div id="selected-students" class="mt-3"></div>
        <div id="selected-students-inputs"></div>
        <small class="form-hint">Busca y selecciona estudiantes del mismo programa. Máximo 2.</small>
    @endif
</div>
@endif



<hr class="mt-4 mb-3">
<h4 class="mt-3">Marcos</h4>
<p class="text-muted mb-2">
    Selecciona el enfoque correspondiente para cada marco.
</p>

@foreach ($frameworks as $framework)
    <div class="mb-3">
        <label class="form-label required d-flex align-items-center gap-1">
            {{ $framework->name }}
            
            {{-- Ícono con tooltip --}}
            <span 
                class="text-muted" 
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="{{ $framework->description }}"
                style="cursor: pointer;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" fill="none"/>
                    <path d="M9.5 9.5a2.5 2.5 0 0 1 5 0 2.4 2.4 0 0 1-2 2.5 2 2 0 0 0-2 2v1" stroke="currentColor" fill="none"/>
                    <circle cx="12" cy="17" r="0.8" fill="currentColor" stroke="none"/>
                </svg>
            </span>
        </label>

        <select 
            name="content_frameworks[{{ $framework->id }}]" 
            class="form-select @error('content_frameworks.' . $framework->id) is-invalid @enderror" 
            required
        >
            <option value="">Selecciona una opción</option>

            @foreach ($framework->contentFrameworks as $content)
                <option value="{{ $content->id }}"
                    @if (old('content_frameworks.' . $framework->id, $projectModel?->contentFrameworkProjects?->firstWhere('content_framework_id', $content->id)?->content_framework_id ?? '') == $content->id) 
                        selected 
                    @endif
                >
                    {{ $content->name }}
                </option>
            @endforeach
        </select>

        @error('content_frameworks.' . $framework->id)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>



<h4 class="mt-4">Datos de contacto</h4>
@if ($isProfessor)
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="contact_first_name" class="form-label required">Nombres</label>
            <input type="text" id="contact_first_name" name="contact_first_name" class="form-control @error('contact_first_name') is-invalid @enderror" value="{{ old('contact_first_name', $prefill['first_name'] ?? '') }}" required>
            @error('contact_first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="contact_last_name" class="form-label required">Apellidos</label>
            <input type="text" id="contact_last_name" name="contact_last_name" class="form-control @error('contact_last_name') is-invalid @enderror" value="{{ old('contact_last_name', $prefill['last_name'] ?? '') }}" required>
            @error('contact_last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="contact_email" class="form-label required">Correo electrónico</label>
            <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', $prefill['email'] ?? '') }}" required>
            @error('contact_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="contact_phone" class="form-label required">Teléfono</label>
            <input type="text" id="contact_phone" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', $prefill['phone'] ?? '') }}" required>
            @error('contact_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@else
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="student_first_name" class="form-label required">Nombres</label>
            <input type="text" id="student_first_name" name="student_first_name" class="form-control @error('student_first_name') is-invalid @enderror" value="{{ old('student_first_name', $prefill['first_name'] ?? '') }}" required>
            @error('student_first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="student_last_name" class="form-label required">Apellidos</label>
            <input type="text" id="student_last_name" name="student_last_name" class="form-control @error('student_last_name') is-invalid @enderror" value="{{ old('student_last_name', $prefill['last_name'] ?? '') }}" required>
            @error('student_last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="student_card_id" class="form-label required">Cédula</label>
            <input type="text" id="student_card_id" name="student_card_id" class="form-control @error('student_card_id') is-invalid @enderror" value="{{ old('student_card_id', $prefill['card_id'] ?? '') }}" required>
            @error('student_card_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="student_email" class="form-label required">Correo electrónico</label>
            <input type="email" id="student_email" name="student_email" class="form-control @error('student_email') is-invalid @enderror" value="{{ old('student_email', $prefill['email'] ?? '') }}" required>
            @error('student_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row g-3 mt-0">
        <div class="col-12 col-md-6">
            <label for="student_phone" class="form-label">Teléfono</label>
            <input type="text" id="student_phone" name="student_phone" class="form-control @error('student_phone') is-invalid @enderror" value="{{ old('student_phone', $prefill['phone'] ?? '') }}">
            @error('student_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif


<script>
document.addEventListener('DOMContentLoaded', function () {
    const lineSelect = document.getElementById('investigation_line_id');
    const areaSelect = document.getElementById('thematic_area_id');

    if (!lineSelect || !areaSelect) {
        return;
    }

    const allAreas = [...areaSelect.options];

    function filterAreas() {
        const selectedLine = lineSelect.value;

        // Reset
        areaSelect.innerHTML = '<option value="">Selecciona un área temática</option>';

        if (!selectedLine) {
            areaSelect.disabled = true;
            return;
        }

        // Filtrar opciones válidas
        const filtered = allAreas.filter(opt => opt.dataset.line === selectedLine);

        filtered.forEach(opt => areaSelect.appendChild(opt));

        areaSelect.disabled = filtered.length === 0;
    }

    // Cuando cambia la línea, filtramos áreas
    lineSelect.addEventListener('change', filterAreas);

    // Si venimos del edit, filtramos automáticamente
    filterAreas();
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('student-search');
    const studentList = document.getElementById('student-list');
    const selectedContainer = document.getElementById('selected-students');
    const hiddenInputsContainer = document.getElementById('selected-students-inputs');

    if (!searchInput || !studentList || !selectedContainer || !hiddenInputsContainer) {
        return;
    }

    let selected = [];

    // Renderizar los seleccionados y los inputs hidden
    function renderSelected() {
        selectedContainer.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        selected.forEach(student => {

            // Mostrar chip visual
            const chip = document.createElement('div');
            chip.className = "d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-body-secondary text-body";
            chip.innerHTML = `
                <span>${student.name} - ${student.card}</span>
                <button type="button" class="btn btn-sm btn-danger remove-student" data-id="${student.id}">X</button>
            `;
            selectedContainer.appendChild(chip);

            // Input hidden que se envía al servidor
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'teammate_ids[]';
            input.value = student.id;
            hiddenInputsContainer.appendChild(input);
        });
    }

    // Filtrar listados
    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.student-option').forEach(btn => {
            const text = btn.textContent.toLowerCase();
            btn.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Seleccionar estudiante
    document.querySelectorAll('.student-option').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            if (selected.length >= 2) {
                alert("Solo puedes seleccionar hasta 2 compañeros.");
                return;
            }

            if (!selected.find(s => s.id === id)) {
                selected.push({
                    id,
                    name: this.dataset.name,
                    card: this.dataset.card
                });
                renderSelected();
            }
        });
    });

    // Eliminar estudiante
    selectedContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-student')) {
            const id = e.target.dataset.id;
            selected = selected.filter(s => s.id !== id);
            renderSelected();
        }
    });
});
</script>