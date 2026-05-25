@extends('tablar::page')

@section('title', 'Nuevo Formato')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.tipos.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">Nuevo Formato</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form action="{{ route('formatos.tipos.store') }}" method="POST">
            @csrf

            {{-- Información del formato --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Información del Formato</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Formato <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo" class="form-control"
                                   value="{{ old('codigo') }}" placeholder="Ej: FOR-INV-007">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <select name="color" class="form-select">
                                @foreach (['blue', 'green', 'orange', 'red', 'purple', 'cyan', 'yellow'] as $c)
                                    <option value="{{ $c }}" {{ old('color') == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ícono (clase Tabler)</label>
                            <input type="text" name="icono" class="form-control"
                                   value="{{ old('icono', 'ti ti-file-text') }}" placeholder="ti ti-file-text">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" rows="2" class="form-control"
                                      placeholder="Breve descripción del formato">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Roles con acceso <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach (['research_staff' => 'Research Staff', 'professor' => 'Profesor', 'committee_leader' => 'Líder de Comité', 'student' => 'Estudiante'] as $valor => $etiqueta)
                                    <div class="col-auto">
                                        <label class="form-check">
                                            <input type="checkbox" name="roles_acceso[]" value="{{ $valor }}" class="form-check-input"
                                                   {{ in_array($valor, old('roles_acceso', [])) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $etiqueta }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles_acceso') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Campos del formulario --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Campos del Formulario</h3>
                    <button type="button" id="btn-agregar" class="btn btn-sm btn-success">
                        <i class="ti ti-plus me-1"></i> Agregar Campo
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Etiqueta (nombre visible)</th>
                                <th style="width:160px">Tipo</th>
                                <th>Sección</th>
                                <th class="text-center" style="width:90px">Requerido</th>
                                <th>Opciones <small class="text-muted">(solo para Lista)</small></th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody id="campos-tbody">
                            @if ($errors->any() && old('campos'))
                                @foreach (old('campos') as $i => $campo)
                                    <tr>
                                        <td><input type="text" name="campos[{{ $i }}][etiqueta]" class="form-control form-control-sm" value="{{ $campo['etiqueta'] }}" required></td>
                                        <td>
                                            <select name="campos[{{ $i }}][tipo]" class="form-select form-select-sm tipo-select" onchange="toggleOpciones(this)">
                                                @foreach (['texto' => 'Texto', 'textarea' => 'Párrafo', 'numero' => 'Número', 'fecha' => 'Fecha', 'hora' => 'Hora', 'select' => 'Lista desplegable', 'checkbox' => 'Casilla'] as $v => $e)
                                                    <option value="{{ $v }}" {{ ($campo['tipo'] ?? '') == $v ? 'selected' : '' }}>{{ $e }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="campos[{{ $i }}][seccion]" class="form-control form-control-sm" value="{{ $campo['seccion'] ?? '' }}"></td>
                                        <td class="text-center"><input type="checkbox" name="campos[{{ $i }}][requerido]" class="form-check-input" {{ isset($campo['requerido']) ? 'checked' : '' }}></td>
                                        <td><textarea name="campos[{{ $i }}][opciones]" class="form-control form-control-sm opciones-field" rows="2" style="{{ ($campo['tipo'] ?? '') === 'select' ? '' : 'display:none' }}">{{ $campo['opciones'] ?? '' }}</textarea></td>
                                        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="ti ti-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    Para <strong>Lista desplegable</strong>: escribe una opción por línea. Formato: <code>valor|Etiqueta</code> (ej: <code>aprobada|Aprobada</code>). Sin el separador, el valor se genera automáticamente.
                </div>
            </div>

            <div class="card">
                <div class="card-footer text-end">
                    <a href="{{ route('formatos.tipos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Crear Formato
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
let idx = {{ $errors->any() && old('campos') ? count(old('campos')) : 0 }};

document.getElementById('btn-agregar').addEventListener('click', function () {
    const tbody = document.getElementById('campos-tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="campos[${idx}][etiqueta]" class="form-control form-control-sm" required placeholder="Ej: Nombre del estudiante"></td>
        <td>
            <select name="campos[${idx}][tipo]" class="form-select form-select-sm tipo-select" onchange="toggleOpciones(this)">
                <option value="texto">Texto</option>
                <option value="textarea">Párrafo</option>
                <option value="numero">Número</option>
                <option value="fecha">Fecha</option>
                <option value="hora">Hora</option>
                <option value="select">Lista desplegable</option>
                <option value="checkbox">Casilla</option>
            </select>
        </td>
        <td><input type="text" name="campos[${idx}][seccion]" class="form-control form-control-sm" placeholder="Ej: 1. Información General"></td>
        <td class="text-center"><input type="checkbox" name="campos[${idx}][requerido]" class="form-check-input"></td>
        <td><textarea name="campos[${idx}][opciones]" class="form-control form-control-sm opciones-field" rows="2" style="display:none" placeholder="aprobada|Aprobada&#10;no_aprobada|No Aprobada"></textarea></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="ti ti-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    idx++;
});

function toggleOpciones(select) {
    const opciones = select.closest('tr').querySelector('.opciones-field');
    opciones.style.display = select.value === 'select' ? 'block' : 'none';
}
</script>

@endsection
