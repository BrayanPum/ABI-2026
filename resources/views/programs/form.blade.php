{{--
    View path: programs/form.blade.php.
    Purpose: Shared form fragment for creating and editing programs.
    Expected variables within this template: $errors, $program (optional), $researchGroups.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($program) && $program->exists;
@endphp

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="mb-3 mb-md-0">
            {{-- Program code identifies the academic offer inside institutional catalogues. --}}
            <label for="code" class="form-label required">Código del programa</label>
            <input
                type="number"
                id="code"
                name="code"
                class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                min="1"
                value="{{ old('code', $program->code ?? '') }}"
                placeholder="Ej: 1203"
                required
            >
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Código institucional del programa (solo números).</small>
        </div>
    </div>
    <div class="col-12 col-md-8">
        {{-- Program name is displayed in every academic publication. --}}
        <label for="name" class="form-label required">Nombre del programa</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            maxlength="100"
            value="{{ old('name', $program->name ?? '') }}"
            placeholder="Ej: Licenciatura en Educación Bilingüe"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Introduce el nombre oficial del programa.</small>
    </div>
</div>

<div class="mt-3">
    {{-- The research group defines thematic areas and available advisors. --}}
    <label for="research_group_id" class="form-label required">Grupo de investigación</label>
    <select
        id="research_group_id"
        name="research_group_id"
        class="form-select {{ $errors->has('research_group_id') ? 'is-invalid' : '' }}"
        required
    >
        <option value="" disabled {{ old('research_group_id', $program->research_group_id ?? '') === '' ? 'selected' : '' }}>Selecciona un grupo…</option>
        @foreach($researchGroups as $id => $groupName)
            <option value="{{ $id }}" {{ (string)old('research_group_id', $program->research_group_id ?? '') === (string)$id ? 'selected' : '' }}>
                {{ $groupName }}
            </option>
        @endforeach
    </select>
    @error('research_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">El grupo determina las líneas y áreas temáticas disponibles para el programa.</small>
</div>

<hr class="my-4">

<div class="form-footer d-flex flex-column flex-md-row justify-content-end gap-2">
    <a href="{{ route('programs.index') }}" class="btn btn-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Submit button sends the program data to the corresponding endpoint. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar programa' : 'Crear programa' }}
    </button>
</div>
