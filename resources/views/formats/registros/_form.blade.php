{{--
    Partial genérico de campos. Recibe:
    - $tipo (FormatoTipo con ->campos cargados)
    - $valores (array [campo_id => valor]) — vacío en create, con datos en edit
--}}

@php $valores ??= []; @endphp

@foreach ($tipo->campos->groupBy('seccion') as $seccion => $campos)

    @if ($seccion)
        <h3 class="mt-3 mb-1">{{ $seccion }}</h3>
        <hr class="mt-0 mb-3">
    @endif

    @foreach ($campos as $campo)
        <div class="mb-3">
            <label class="form-label">
                {{ $campo->etiqueta }}
                @if (!$campo->requerido)
                    <span class="text-muted">(opcional)</span>
                @endif
            </label>

            @switch($campo->tipo)

                @case('texto')
                    <input type="text"
                           name="campo_{{ $campo->id }}"
                           class="form-control @error('campo_' . $campo->id) is-invalid @enderror"
                           value="{{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') }}"
                           {{ $campo->requerido ? 'required' : '' }}>
                    @break

                @case('textarea')
                    <textarea name="campo_{{ $campo->id }}"
                              rows="3"
                              class="form-control @error('campo_' . $campo->id) is-invalid @enderror"
                              {{ $campo->requerido ? 'required' : '' }}>{{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') }}</textarea>
                    @break

                @case('numero')
                    <input type="number"
                           name="campo_{{ $campo->id }}"
                           class="form-control @error('campo_' . $campo->id) is-invalid @enderror"
                           value="{{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') }}"
                           {{ $campo->requerido ? 'required' : '' }}>
                    @break

                @case('fecha')
                    <input type="date"
                           name="campo_{{ $campo->id }}"
                           class="form-control @error('campo_' . $campo->id) is-invalid @enderror"
                           value="{{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') }}"
                           {{ $campo->requerido ? 'required' : '' }}>
                    @break

                @case('hora')
                    <input type="time"
                           name="campo_{{ $campo->id }}"
                           class="form-control @error('campo_' . $campo->id) is-invalid @enderror"
                           value="{{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') }}"
                           {{ $campo->requerido ? 'required' : '' }}>
                    @break

                @case('checkbox')
                    <div class="form-check">
                        <input type="checkbox"
                               name="campo_{{ $campo->id }}"
                               class="form-check-input"
                               value="1"
                               {{ old('campo_' . $campo->id, $valores[$campo->id] ?? '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $campo->etiqueta }}</label>
                    </div>
                    @break

                @case('select')
                    <select name="campo_{{ $campo->id }}"
                            class="form-select @error('campo_' . $campo->id) is-invalid @enderror"
                            {{ $campo->requerido ? 'required' : '' }}>
                        <option value="">Seleccione...</option>
                        @foreach ($campo->opciones ?? [] as $opcion)
                            <option value="{{ $opcion['valor'] }}"
                                    {{ old('campo_' . $campo->id, $valores[$campo->id] ?? '') == $opcion['valor'] ? 'selected' : '' }}>
                                {{ $opcion['etiqueta'] }}
                            </option>
                        @endforeach
                    </select>
                    @break

            @endswitch

            @error('campo_' . $campo->id)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endforeach

@endforeach
