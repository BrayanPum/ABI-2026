{{--
    Partial path: departments/form.blade.php.
    Purpose: Shared Tablar form fragment for department create and edit screens.
    Expected variables: $department (optional) and $errors for validation feedback.
    The hosting view must render the surrounding submit and cancel actions.
--}}
{{-- Wrapper for the department name input field and its validation feedback. --}}
<div class="mb-3">
    {{-- Label clarifies the required nature of the department name field. --}}
    <label for="name" class="form-label required">Nombre del departamento</label>
    {{--
        Text input where administrators provide the official department name.
        Old input is preserved to avoid data loss when validation fails, and the
        field is marked invalid when server-side validation reports an error.
    --}}
    {{-- Input element used to capture the 'name' value. --}}
    <input type="text"
           id="name"
           name="name"
           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
           maxlength="100"
           value="{{ old('name', $department->name ?? '') }}"
           placeholder="Ej: Antioquia"
           required>
    {{-- Validation error message displayed when the name field fails validation. --}}
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    {{-- Helper text provides guidance about the type of value expected. --}}
    <small class="form-hint">Introduce el nombre oficial del departamento administrativo.</small>
</div>

