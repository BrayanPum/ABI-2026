{{--
    View path: research-groups/form.blade.php.
    Purpose: Renders the form.blade view for the Research Groups module.
    Expected variables within this template: $errors, $isEdit, $message, $researchGroup.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($researchGroup) && $researchGroup->exists;
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Nombre del grupo'. --}}
            <label for="name" class="form-label required">Nombre del grupo</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   placeholder="Ej: Grupo de Investigación en Educación Bilingüe"
                   maxlength="150"
                   value="{{ old('name', $researchGroup->name ?? '') }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Utiliza un nombre descriptivo que identifique al grupo en la red académica.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Sigla'. --}}
            <label for="initials" class="form-label required">Sigla</label>
            {{-- Input element used to capture the 'initials' value. --}}
            <input type="text"
                   id="initials"
                   name="initials"
                   class="form-control {{ $errors->has('initials') ? 'is-invalid' : '' }}"
                   placeholder="Ej: GIEB"
                   maxlength="20"
                   value="{{ old('initials', $researchGroup->initials ?? '') }}"
                   required>
            @error('initials')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Se recomienda usar entre 3 y 6 caracteres en mayúscula.</small>
        </div>
    </div>
</div>

<div class="mb-3">
    {{-- Label describing the purpose of 'Descripción'. --}}
    <label for="description" class="form-label required">Descripción</label>
    {{-- Multiline textarea allowing a detailed description for 'details'. --}}
    <textarea id="description"
              name="description"
              rows="4"
              class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Describe los objetivos, trayectoria y focos de trabajo del grupo"
              required>{{ old('description', $researchGroup->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">La descripción debe tener al menos 10 caracteres y puede incluir información sobre los proyectos representativos.</small>
</div>

<hr class="my-4">

<div class="form-footer d-flex justify-content-between align-items-center">
    <a href="{{ route('research-groups.index') }}" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Button element of type 'submit' to trigger the intended action. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar grupo' : 'Crear grupo' }}
    </button>
</div>
