{{--
    Partial path: content-versions/form.blade.php.
    Purpose: Shared form fragment for selecting a content, version, and captured value.
    Expected variables: $contentVersion (optional), $contentId (optional), $versionId (optional).
--}}
@php
    $model = $contentVersion ?? null;
    $selectedContent = old('content_id', $contentId ?? ($model->content_id ?? ''));
    $selectedVersion = old('version_id', $versionId ?? ($model->version_id ?? ''));
    $value = old('value', $model->value ?? '');
@endphp

<div class="mb-3">
    <label for="content_id" class="form-label required">Contenido</label>
    <select id="content_id" name="content_id" class="form-select" required data-selected-content="{{ $selectedContent }}">
        <option value="">Selecciona un contenido</option>
    </select>
    <small class="form-hint">Busca el contenido por nombre o utiliza el selector para filtrar.</small>
    <div class="invalid-feedback d-none" data-feedback-for="content_id"></div>
</div>

<div class="mb-3">
    <label for="version_id" class="form-label required">Versión del proyecto</label>
    <select id="version_id" name="version_id" class="form-select" required data-selected-version="{{ $selectedVersion }}">
        <option value="">Selecciona una versión</option>
    </select>
    <small class="form-hint">Selecciona la versión del proyecto a la que pertenece el contenido.</small>
    <div class="invalid-feedback d-none" data-feedback-for="version_id"></div>
</div>

<div class="mb-3">
    <label for="value" class="form-label required">Valor diligenciado</label>
    <textarea
        id="value"
        name="value"
        class="form-control"
        rows="4"
        placeholder="Registra el valor capturado en la versión"
        required
    >{{ $value }}</textarea>
    <div class="invalid-feedback d-none" data-feedback-for="value"></div>
</div>
