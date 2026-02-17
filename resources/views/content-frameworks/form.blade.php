{{--
    View path: content-frameworks/form.blade.php.
    Purpose: Shared form fragment used on create and edit screens for framework contents.
    Expected variables: $contentFramework (optional), $frameworks (optional), $prefw (optional).
--}}
@php
    $record = $contentFramework ?? null;
    $preselectedFrameworkId = old('framework_id', $prefw ?? ($record->framework_id ?? null));

    if (!isset($frameworks)) {
        try {
            $frameworks = \App\Models\Framework::orderBy('name')->pluck('name', 'id')->toArray();
        } catch (\Throwable $exception) {
            $frameworks = [];
        }
    }
@endphp

<div class="row g-3">
    <div class="col-12">
        <label for="name" class="form-label required">Nombre del contenido</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            placeholder="Ej: Competencias comunicativas"
            value="{{ old('name', $record->name ?? '') }}"
            maxlength="255"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Utiliza un nombre breve y descriptivo.</small>
    </div>

    <div class="col-12">
        <label for="description" class="form-label required">Descripción</label>
        <textarea
            id="description"
            name="description"
            class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
            rows="4"
            placeholder="Describe el objetivo del contenido dentro del marco."
            maxlength="1000"
            required
        >{{ old('description', $record->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Explica en qué contexto se aplicará este contenido.</small>
    </div>

    <div class="col-12 col-lg-6">
        <label for="framework_id" class="form-label required">Marco asociado</label>
        <select
            id="framework_id"
            name="framework_id"
            class="form-select {{ $errors->has('framework_id') ? 'is-invalid' : '' }}"
            required
        >
            <option value="" disabled {{ $preselectedFrameworkId ? '' : 'selected' }}>Selecciona un framework…</option>
            @foreach($frameworks as $frameworkId => $frameworkName)
                <option value="{{ $frameworkId }}" {{ (string)$preselectedFrameworkId === (string)$frameworkId ? 'selected' : '' }}>
                    {{ $frameworkName }}
                </option>
            @endforeach
        </select>
        @error('framework_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">El contenido quedará vinculado a este marco.</small>
        @if($preselectedFrameworkId)
            <div class="mt-2">
                <a class="badge bg-azure-lt text-decoration-none" href="{{ route('frameworks.show', $preselectedFrameworkId) }}">Ver framework seleccionado</a>
            </div>
        @endif
    </div>

    @if($record?->id)
        <div class="col-12 col-lg-6">
            <div class="alert alert-info h-100 mb-0">
                <h4 class="alert-title">Información del registro</h4>
                <p class="mb-1">ID: #{{ $record->id }}</p>
                <p class="mb-1">Creado: {{ $record->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                <p class="mb-0">Actualizado: {{ $record->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="d-flex justify-content-between flex-column flex-sm-row gap-2">
            <a href="{{ route('content-frameworks.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                {{ $record?->id ? 'Actualizar contenido' : 'Guardar contenido' }}
            </button>
        </div>
    </div>
</div>
