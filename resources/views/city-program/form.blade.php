{{--
    Partial path: city-program/form.blade.php.
    Purpose: Shared form fragment for creating and editing city-program assignments.
    Expected variables: $cities, $programs, $cityProgram (optional), $errors.
--}}
@php
    $isEdit = isset($cityProgram) && $cityProgram->exists;
@endphp

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            {{-- Dropdown lets administrators choose the city. --}}
            <label for="city_id" class="form-label required">Ciudad</label>
            <select id="city_id"
                    name="city_id"
                    class="form-select {{ $errors->has('city_id') ? 'is-invalid' : '' }}"
                    required>
                <option value="" disabled {{ old('city_id', $cityProgram->city_id ?? '') === '' ? 'selected' : '' }}>Selecciona una ciudad…</option>
                @foreach($cities as $id => $cityName)
                    <option value="{{ $id }}" {{ (string) old('city_id', $cityProgram->city_id ?? '') === (string) $id ? 'selected' : '' }}>
                        {{ $cityName }}
                    </option>
                @endforeach
            </select>
            @error('city_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">La relación vinculará el programa con la ciudad seleccionada.</small>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="mb-3">
            {{-- Dropdown lets administrators choose the academic program. --}}
            <label for="program_id" class="form-label required">Programa académico</label>
            <select id="program_id"
                    name="program_id"
                    class="form-select {{ $errors->has('program_id') ? 'is-invalid' : '' }}"
                    required>
                <option value="" disabled {{ old('program_id', $cityProgram->program_id ?? '') === '' ? 'selected' : '' }}>Selecciona un programa…</option>
                @foreach($programs as $id => $programName)
                    <option value="{{ $id }}" {{ (string) old('program_id', $cityProgram->program_id ?? '') === (string) $id ? 'selected' : '' }}>
                        {{ $programName }}
                    </option>
                @endforeach
            </select>
            @error('program_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Selecciona el programa que se impartirá en la ciudad indicada.</small>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="form-footer d-flex flex-column flex-md-row justify-content-end gap-2">
    <a href="{{ route('city-program.index') }}" class="btn btn-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Submit button stores the selected relationship. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar relación' : 'Crear relación' }}
    </button>
</div>
