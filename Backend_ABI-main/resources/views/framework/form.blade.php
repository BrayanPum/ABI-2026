{{--
    View path: framework/form.blade.php.
    Purpose: Shared Tablar form fragment for creating and editing academic frameworks.
    Expected variables within this template: $errors, $framework (optional), $showSubmit (optional).
--}}
@php
    $frameworkModel = $framework ?? null;
    $showSubmit = $showSubmit ?? (!isset($frameworkModel) || !$frameworkModel?->exists);
@endphp

<div class="mb-3">
    <label for="name" class="form-label required">Nombre del marco</label>
    <input
        type="text"
        id="name"
        name="name"
        maxlength="255"
        value="{{ old('name', $frameworkModel->name ?? '') }}"
        class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
        placeholder="Ej. Currículo Nacional 2025"
        required
        autocomplete="off"
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Utiliza un nombre descriptivo para identificar el marco curricular.</small>
</div>

<div class="mb-3">
    <label for="description" class="form-label required">Descripción</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        maxlength="1000"
        class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
        placeholder="Resume los objetivos y el alcance de este marco."
        required
    >{{ old('description', $frameworkModel->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Incluye detalles suficientes para que otros usuarios entiendan su propósito.</small>
</div>

<div class="mb-3">
    <label for="link" class="form-label">Enlace de referencia</label>
    <input
        type="url"
        id="link"
        name="link"
        value="{{ old('link', $frameworkModel->link ?? '') }}"
        class="form-control {{ $errors->has('link') ? 'is-invalid' : '' }}"
        placeholder="https://ejemplo.edu/framework"
    >
    @error('link')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Proporciona una URL pública al documento oficial o repositorio relacionado (opcional).</small>
</div>

<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="start_year" class="form-label required">Año de inicio</label>
        <input
            type="number"
            id="start_year"
            name="start_year"
            min="1900"
            max="{{ date('Y') + 50 }}"
            value="{{ old('start_year', $frameworkModel->start_year ?? date('Y')) }}"
            class="form-control {{ $errors->has('start_year') ? 'is-invalid' : '' }}"
            required
        >
        @error('start_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Indica el año en que el marco entra en vigencia.</small>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_year" class="form-label">Año de finalización</label>
        <input
            type="number"
            id="end_year"
            name="end_year"
            min="{{ old('start_year', $frameworkModel->start_year ?? date('Y')) }}"
            max="{{ date('Y') + 50 }}"
            value="{{ old('end_year', $frameworkModel->end_year ?? '') }}"
            class="form-control {{ $errors->has('end_year') ? 'is-invalid' : '' }}"
            placeholder="Dejar vacío si es indefinido"
        >
        @error('end_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Completa este campo solo si la vigencia tiene una fecha de cierre.</small>
    </div>
</div>

@if($showSubmit)
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('frameworks.index') }}" class="btn btn-link">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar marco</button>
    </div>
@endif
