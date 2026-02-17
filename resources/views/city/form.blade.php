{{--
    Partial path: city/form.blade.php.
    Purpose: Shared Tablar form fragment for city create and edit screens.
    Expected variables within this template: $city (optional), $departments (array-like), $errors.
    The surrounding view is responsible for rendering actions such as cancel or submit buttons.
--}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Nombre de la ciudad'. --}}
            <label for="name" class="form-label required">Nombre de la ciudad</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   maxlength="100"
                   value="{{ old('name', $city->name ?? '') }}"
                   placeholder="Ej: Medellín"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Nombre oficial de la ciudad en el departamento seleccionado.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Departamento'. --}}
            <label for="department_id" class="form-label required">Departamento</label>
            {{-- Dropdown presenting the available options for 'department_id'. --}}
            <select id="department_id"
                    name="department_id"
                    class="form-select {{ $errors->has('department_id') ? 'is-invalid' : '' }}"
                    required>
                <option value="" disabled {{ old('department_id', $city->department_id ?? '') === '' ? 'selected' : '' }}>Selecciona un departamento…</option>
                @foreach($departments as $id => $departmentName)
                    <option value="{{ $id }}" {{ (string)old('department_id', $city->department_id ?? '') === (string)$id ? 'selected' : '' }}>
                        {{ $departmentName }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">La ciudad se asociará automáticamente al departamento elegido.</small>
        </div>
    </div>
</div>
