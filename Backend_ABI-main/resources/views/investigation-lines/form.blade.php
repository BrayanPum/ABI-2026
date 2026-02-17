{{--
    View path: investigation-lines/form.blade.php.
    Purpose: Renders the form.blade view for the Investigation Lines module.
    Expected variables within this template: $errors, $groupName, $id, $investigationLine, $isEdit, $message, $researchGroups, $selectedGroup.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($investigationLine) && $investigationLine->exists;
    $selectedGroup = old('research_group_id', $investigationLine->research_group_id ?? request('research_group_id'));
@endphp

<div class="mb-3">
    {{-- Label describing the purpose of 'Nombre de la línea'. --}}
    <label for="name" class="form-label required">Nombre de la línea</label>
    {{-- Input element used to capture the 'name' value. --}}
    <input type="text"
           id="name"
           name="name"
           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
           maxlength="100"
           value="{{ old('name', $investigationLine->name ?? '') }}"
           placeholder="Ej: Innovación en educación bilingüe"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Introduce un nombre representativo y único para la línea de investigación.</small>
</div>

<div class="mb-3">
    {{-- Label describing the purpose of 'Descripción'. --}}
    <label for="description" class="form-label required">Descripción</label>
    {{-- Multiline textarea allowing a detailed description for 'details'. --}}
    <textarea id="description"
              name="description"
              rows="4"
              class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Describe el enfoque, objetivos y alcance de la línea"
              required>{{ old('description', $investigationLine->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Especifica el impacto esperado, las temáticas y metodologías que se abordan.</small>
</div>

<div class="mb-3">
    {{-- Label describing the purpose of 'Grupo de investigación'. --}}
    <label for="research_group_id" class="form-label required">Grupo de investigación</label>
    {{-- Dropdown presenting the available options for 'research_group_id'. --}}
    <select id="research_group_id"
            name="research_group_id"
            class="form-select {{ $errors->has('research_group_id') ? 'is-invalid' : '' }}"
            required>
        <option value="" disabled {{ empty($selectedGroup) ? 'selected' : '' }}>Selecciona un grupo…</option>
        @foreach($researchGroups as $id => $groupName)
            <option value="{{ $id }}" {{ (string)$selectedGroup === (string)$id ? 'selected' : '' }}>{{ $groupName }}</option>
        @endforeach
    </select>
    @error('research_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Las áreas temáticas disponibles dependerán del grupo seleccionado.</small>
</div>

