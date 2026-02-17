{{--
    View path: content-framework-project/form.blade.php.
    Purpose: Shared form fragment for linking projects with content frameworks.
    Expected variables: $contentFrameworkProject (optional), $projects (array), $contentFrameworks (array), $projectId (optional), $contentFrameworkId (optional).
--}}
@php
    $record = $contentFrameworkProject ?? null;
    $selectedProjectId = old('project_id', $projectId ?? ($record->project_id ?? null));
    $selectedContentFrameworkId = old('content_framework_id', $contentFrameworkId ?? ($record->content_framework_id ?? null));
@endphp

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <label for="project_id" class="form-label required">Proyecto</label>
        <select
            id="project_id"
            name="project_id"
            class="form-select {{ $errors->has('project_id') ? 'is-invalid' : '' }}"
            required
        >
            <option value="" disabled {{ $selectedProjectId ? '' : 'selected' }}>Selecciona un proyecto…</option>
            @foreach($projects as $id => $title)
                <option value="{{ $id }}" {{ (string)$selectedProjectId === (string)$id ? 'selected' : '' }}>
                    {{ $title }}
                </option>
            @endforeach
        </select>
        @error('project_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Elige el proyecto al que deseas asociar el contenido.</small>
    </div>

    <div class="col-12 col-lg-6">
        <label for="content_framework_id" class="form-label required">Contenido del marco</label>
        <select
            id="content_framework_id"
            name="content_framework_id"
            class="form-select {{ $errors->has('content_framework_id') ? 'is-invalid' : '' }}"
            required
        >
            <option value="" disabled {{ $selectedContentFrameworkId ? '' : 'selected' }}>Selecciona un contenido…</option>
            @foreach($contentFrameworks as $id => $name)
                <option value="{{ $id }}" {{ (string)$selectedContentFrameworkId === (string)$id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('content_framework_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Este contenido quedará vinculado al proyecto seleccionado.</small>
    </div>

    @if($record?->id)
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <h4 class="alert-title">Información de la asignación</h4>
                <p class="mb-1">ID: #{{ $record->id }}</p>
                <p class="mb-1">Creado: {{ $record->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                <p class="mb-0">Actualizado: {{ $record->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="d-flex justify-content-between flex-column flex-sm-row gap-2">
            <a href="{{ route('content-framework-project.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $record?->id ? 'Actualizar asignación' : 'Guardar asignación' }}
            </button>
        </div>
    </div>
</div>
