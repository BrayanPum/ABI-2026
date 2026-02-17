{{--
    Partial path: versions/form.blade.php.
    Purpose: Shared Tablar form fragment used on version create and edit screens.
    Expected variables:
    - $version (optional): existing version model data provided to the template.
    - $projectId (optional): explicit project ID value when loading the form via JS.
--}}
@php
    $versionModel = $version ?? null;
    $projectId = old('project_id', $projectId ?? ($versionModel->project_id ?? ''));
@endphp

<div class="mb-3">
    <label for="project_id" class="form-label required">ID del proyecto</label>
    <div class="input-group">
        <span class="input-group-text">#</span>
        <input
            type="number"
            min="1"
            step="1"
            id="project_id"
            name="project_id"
            value="{{ $projectId }}"
            class="form-control"
            placeholder="Ej. 12"
            required
            autocomplete="off"
        >
    </div>
    <small class="form-hint">Utiliza el identificador numérico del proyecto asociado.</small>
    <div class="invalid-feedback d-none" data-feedback-for="project_id"></div>
</div>

<div class="mb-3">
    <label class="form-label">Información del proyecto</label>
    <div class="card card-sm shadow-none border border-dashed" id="project-preview" hidden>
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-azure-lt" id="project-preview-id">ID —</span>
                <div>
                    <strong id="project-preview-name">Proyecto sin cargar</strong>
                    <div class="text-secondary" id="project-preview-description">Ingresa un ID válido para consultar detalles.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-secondary" id="project-preview-empty">Ingresa un ID válido para mostrar el nombre y código del proyecto.</div>
</div>

@push('css')
    <style>
        .form-label.required::after {
            content: ' *';
            color: var(--tblr-danger);
        }
    </style>
@endpush
