{{--
    View path: programs/form.blade.php.
    Purpose: Shared form fragment for creating and editing programs.
    Expected variables within this template: $cities, $errors, $program (optional), $researchGroups.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($program) && $program->exists;
    $selectedCityIds = collect(old('city_ids', $program->cities->pluck('id')->all() ?? []))
        ->map(fn ($cityId) => (string) $cityId)
        ->all();
    $selectedCities = $cities
        ->filter(fn ($city) => in_array((string) $city->id, $selectedCityIds, true))
        ->sortBy('name')
        ->values();
@endphp

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="mb-3 mb-md-0">
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
    <label for="research_group_id" class="form-label required">Grupo de investigación</label>
    <select
        id="research_group_id"
        name="research_group_id"
        class="form-select {{ $errors->has('research_group_id') ? 'is-invalid' : '' }}"
        required
    >
        <option value="" disabled {{ old('research_group_id', $program->research_group_id ?? '') === '' ? 'selected' : '' }}>Selecciona un grupo...</option>
        @foreach($researchGroups as $id => $groupName)
            <option value="{{ $id }}" {{ (string) old('research_group_id', $program->research_group_id ?? '') === (string) $id ? 'selected' : '' }}>
                {{ $groupName }}
            </option>
        @endforeach
    </select>
    @error('research_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">El grupo determina las líneas y áreas temáticas disponibles para el programa.</small>
</div>

<div class="mt-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="card-title mb-1">Ciudades asociadas al programa</h3>
            <p class="text-muted mb-0">Selecciona desde una sola lista las ciudades disponibles junto con su departamento.</p>
        </div>
        <span class="badge bg-azure-lt">{{ $cities->count() }} disponibles</span>
    </div>

    @if($errors->has('city_ids') || $errors->has('city_ids.*'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('city_ids') ?: $errors->first('city_ids.*') }}
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <label for="city-filter" class="form-label">Buscar ciudad o departamento</label>
            <div class="input-icon mb-2">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="10" cy="10" r="7" />
                        <line x1="21" y1="21" x2="15" y2="15" />
                    </svg>
                </span>
                <input
                    type="text"
                    id="city-filter"
                    class="form-control"
                    placeholder="Escribe una ciudad o un departamento"
                    autocomplete="off"
                >
            </div>

            <label for="city_ids" class="form-label required">Lista de ciudades</label>
            <select
                id="city_ids"
                name="city_ids[]"
                class="form-select {{ $errors->has('city_ids') || $errors->has('city_ids.*') ? 'is-invalid' : '' }}"
                multiple
                size="12"
                aria-describedby="city-selection-help"
            >
                @forelse($cities as $city)
                    <option
                        value="{{ $city->id }}"
                        data-city-name="{{ mb_strtolower($city->name) }}"
                        data-city-label="{{ $city->name }}"
                        data-department="{{ $city->department->name ?? 'Sin departamento' }}"
                        data-department-name="{{ mb_strtolower($city->department->name ?? 'Sin departamento') }}"
                        {{ in_array((string) $city->id, $selectedCityIds, true) ? 'selected' : '' }}
                    >
                        {{ $city->name }} - {{ $city->department->name ?? 'Sin departamento' }}
                    </option>
                @empty
                    <option value="" disabled>No hay ciudades disponibles para asociar en este momento.</option>
                @endforelse
            </select>
            <small id="city-selection-help" class="form-hint">
                Usa <strong>Ctrl</strong> o <strong>Cmd</strong> para seleccionar varias ciudades. Al editar, quitar una ciudad solo elimina la relación con el programa.
            </small>
        </div>

        <div class="col-12 col-lg-5">
            <div class="border rounded h-100">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between gap-2">
                    <div>
                        <h4 class="mb-1">Ciudades seleccionadas</h4>
                        <p class="text-muted mb-0 small">Resumen rápido de la selección actual.</p>
                    </div>
                    <span class="badge bg-azure-lt" id="selected-city-count">{{ $selectedCities->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>Ciudad</th>
                                <th>Departamento</th>
                            </tr>
                        </thead>
                        <tbody id="selected-city-table">
                            @forelse($selectedCities as $selectedCity)
                                <tr>
                                    <td>{{ $selectedCity->name }}</td>
                                    <td class="text-muted">{{ $selectedCity->department->name ?? 'Sin departamento' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        Todavía no has seleccionado ciudades.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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

    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar programa' : 'Crear programa' }}
    </button>
</div>

@once
    @push('css')
        <style>
            #city_ids {
                min-height: 20rem;
            }
        </style>
    @endpush

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterInput = document.getElementById('city-filter');
                const citySelect = document.getElementById('city_ids');
                const selectedCount = document.getElementById('selected-city-count');
                const selectedTable = document.getElementById('selected-city-table');

                if (!filterInput || !citySelect || !selectedCount || !selectedTable) {
                    return;
                }

                const renderSelectedCities = () => {
                    const selectedOptions = Array.from(citySelect.options)
                        .filter(option => option.selected && option.value !== '');

                    selectedCount.textContent = String(selectedOptions.length);

                    if (selectedOptions.length === 0) {
                        selectedTable.innerHTML = `
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">
                                    Todavía no has seleccionado ciudades.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    selectedTable.innerHTML = selectedOptions
                        .map(option => `
                            <tr>
                                <td>${option.dataset.cityLabel ?? ''}</td>
                                <td class="text-muted">${option.dataset.department ?? 'Sin departamento'}</td>
                            </tr>
                        `)
                        .join('');
                };

                const filterCities = () => {
                    const query = filterInput.value.trim().toLowerCase();

                    Array.from(citySelect.options).forEach(option => {
                        if (!option.value) {
                            return;
                        }

                        const cityName = option.dataset.cityName ?? '';
                        const departmentName = option.dataset.departmentName ?? '';
                        const matches = cityName.includes(query) || departmentName.includes(query) || option.selected;
                        option.hidden = query !== '' && !matches;
                    });
                };

                citySelect.addEventListener('change', renderSelectedCities);
                filterInput.addEventListener('input', filterCities);

                renderSelectedCities();
                filterCities();
            });
        </script>
    @endpush
@endonce
