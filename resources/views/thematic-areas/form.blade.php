{{--
    Determine the context in which the form is rendered and prepare helper
    collections used by the select elements. The grouped investigation lines make
    it easier for the user to find the desired option.
--}}
@php
    $isEdit = isset($thematicArea) && $thematicArea->exists;
    $selectedLine = old('investigation_line_id', $thematicArea->investigation_line_id ?? request('investigation_line_id'));
    $linesByGroup = $investigationLines->groupBy(fn($line) => $line->researchGroup->name ?? 'Sin grupo');
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Identify the thematic area with a descriptive name. --}}
            <label for="name" class="form-label required">Nombre del área</label>
            {{-- Preserve the previous value and highlight validation errors when needed. --}}
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   maxlength="100"
                   value="{{ old('name', $thematicArea->name ?? '') }}"
                   placeholder="Ej: Evaluación de competencias comunicativas"
                   required>
            {{-- Feedback slot for validation messages related to the name field. --}}
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Usa un nombre claro y conciso que identifique la temática.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Select the investigation line to which this thematic area belongs. --}}
            <label for="investigation_line_id" class="form-label required">Línea de investigación</label>
            {{-- Dropdown presenting the available options for 'investigation_line_id'. --}}
            <select id="investigation_line_id"
                    name="investigation_line_id"
                    class="form-select {{ $errors->has('investigation_line_id') ? 'is-invalid' : '' }}"
                    required>
                <option value="" disabled {{ empty($selectedLine) ? 'selected' : '' }}>Selecciona una línea…</option>
                @foreach($linesByGroup as $groupName => $lines)
                    {{-- Group investigation lines by research group for easier scanning. --}}
                    <optgroup label="{{ $groupName }}">
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}" {{ (string)$selectedLine === (string)$line->id ? 'selected' : '' }}>
                                {{ $line->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('investigation_line_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Elige la línea que agrupa esta área temática. Las líneas se muestran por grupo de investigación.</small>
        </div>
    </div>
</div>

<div class="mb-3">
    {{-- Provide a detailed explanation of the thematic area's scope. --}}
    <label for="description" class="form-label required">Descripción</label>
    {{-- Multiline textarea allowing a detailed description for 'details'. --}}
    <textarea id="description"
              name="description"
              rows="4"
              class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Describe los objetivos y alcances del área temática"
              required>{{ old('description', $thematicArea->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Indica qué aspectos aborda el área, los recursos y resultados esperados (mínimo 10 caracteres).</small>
</div>

<hr class="my-4">

<div class="form-footer d-flex justify-content-between align-items-center">
    {{-- Return to the index without applying changes. --}}
    <a href="{{ route('thematic-areas.index') }}" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Submit the form to either create or update the thematic area. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar área' : 'Crear área' }}
    </button>
</div>
