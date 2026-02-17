{{--
    Partial path: contents/form.blade.php.
    Purpose: Shared Tablar form fragment for creating or updating catalog contents.
    Expected variables:
    - $content (optional): existing content model instance for edit contexts.
    - $showRoleSelector (bool, optional): toggles the display of the role checkboxes.
    - $selectedRoles (array|string, optional): pre-selected roles when rendering the form.
--}}
@php
    use App\Models\Content;

    $contentModel = $content ?? null;
    $showRoleSelector = $showRoleSelector ?? true;
    $roleLabels = $roleLabels ?? [
        'research_staff' => 'Equipo de investigación',
        'professor' => 'Profesor',
        'student' => 'Estudiante',
        'committee_leader' => 'Líder de comité',
    ];

    $selectedRoles = $selectedRoles ?? ($contentModel->roles ?? []);
    if (! is_array($selectedRoles)) {
        $selectedRoles = array_filter([$selectedRoles]);
    }
    $selectedRoles = array_map('strval', $selectedRoles);
@endphp

<div class="mb-3">
    <label for="name" class="form-label required">Nombre</label>
    <input
        type="text"
        id="name"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $contentModel->name ?? '') }}"
        maxlength="255"
        required
        placeholder="Ej. Diseñar actividades colaborativas"
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea
        id="description"
        name="description"
        class="form-control @error('description') is-invalid @enderror"
        rows="4"
        placeholder="Describe brevemente el objetivo o alcance del contenido"
    >{{ old('description', $contentModel->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if($showRoleSelector)
    <div class="mb-3">
        <label class="form-label required">Roles con acceso</label>
        <div class="row g-2">
            @foreach(Content::ALLOWED_ROLES as $role)
                <div class="col-12 col-md-6">
                    <label class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role }}"
                            {{ in_array($role, $selectedRoles, true) ? 'checked' : '' }}
                        >
                        <span class="form-check-label">{{ $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}</span>
                    </label>
                </div>
            @endforeach
        </div>
        <small class="form-hint">Selecciona al menos un rol para definir quién puede administrar el contenido.</small>
        @error('roles')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
@endif

@push('css')
    <style>
        .form-label.required::after {
            content: ' *';
            color: var(--tblr-danger);
        }
    </style>
@endpush
