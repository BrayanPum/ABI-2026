@extends('tablar::page')

@section('title', 'Editar Formato: ' . $tipo->nombre)

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.tipos.show', $tipo) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">Editar: {{ $tipo->nombre }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @if ($tipo->registros()->count() > 0)
            <div class="alert alert-warning">
                <strong>Atención:</strong> Este formato tiene {{ $tipo->registros()->count() }} registro(s) existente(s).
                Modificar o eliminar campos borrará los datos de esos registros.
            </div>
        @endif

        <form action="{{ route('formatos.tipos.update', $tipo) }}" method="POST">
            @csrf
            @method('PUT')

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
                                   value="{{ old('nombre', $tipo->nombre) }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Código</label>
                            <input type="text" name="codigo" class="form-control"
                                   value="{{ old('codigo', $tipo->codigo) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <select name="color" class="form-select">
                                @foreach (['blue', 'green', 'orange', 'red', 'purple', 'cyan', 'yellow'] as $c)
                                    <option value="{{ $c }}" {{ old('color', $tipo->color) == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ícono (clase Tabler)</label>
                            <input type="text" name="icono" class="form-control"
                                   value="{{ old('icono', $tipo->icono) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion', $tipo->descripcion) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Roles con acceso <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach (['research_staff' => 'Research Staff', 'professor' => 'Profesor', 'committee_leader' => 'Líder de Comité', 'student' => 'Estudiante'] as $valor => $etiqueta)
                                    <div class="col-auto">
                                        <label class="form-check">
                                            <input type="checkbox" name="roles_acceso[]" value="{{ $valor }}" class="form-check-input"
                                                   {{ in_array($valor, old('roles_acceso', $tipo->roles_acceso ?? [])) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $etiqueta }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                <th>Etiqueta</th>
                                <th style="width:160px">Tipo</th>
                                <th>Sección</th>
                                <th class="text-center" style="width:90px">Requerido</th>
                                <th>Opciones <small class="text-muted">(solo Lista)</small></th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody id="campos-tbody">
                            @foreach ($tipo->campos as $i => $campo)
                                @php
                                    $opcionesTexto = '';
                                    if ($campo->tipo === 'select' && $campo->opciones) {
                                        $opcionesTexto = collect($campo->opciones)->map(fn($o) => $o['valor'] . '|' . $o['etiqueta'])->implode("\n");
                                    }
                                @endphp
                                <tr>
                                    <td><input type="text" name="campos[{{ $i }}][etiqueta]" class="form-control form-control-sm" value="{{ old("campos.$i.etiqueta", $campo->etiqueta) }}" required></td>
                                    <td>
                                        <select name="campos[{{ $i }}][tipo]" class="form-select form-select-sm tipo-select" onchange="toggleOpciones(this)">
                                            @foreach (['texto' => 'Texto', 'textarea' => 'Párrafo', 'numero' => 'Número', 'fecha' => 'Fecha', 'hora' => 'Hora', 'select' => 'Lista desplegable', 'checkbox' => 'Casilla'] as $v => $e)
                                                <option value="{{ $v }}" {{ old("campos.$i.tipo", $campo->tipo) == $v ? 'selected' : '' }}>{{ $e }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" name="campos[{{ $i }}][seccion]" class="form-control form-control-sm" value="{{ old("campos.$i.seccion", $campo->seccion) }}"></td>
                                    <td class="text-center"><input type="checkbox" name="campos[{{ $i }}][requerido]" class="form-check-input" {{ old("campos.$i.requerido", $campo->requerido) ? 'checked' : '' }}></td>
                                    <td><textarea name="campos[{{ $i }}][opciones]" class="form-control form-control-sm opciones-field" rows="2" style="{{ $campo->tipo === 'select' ? '' : 'display:none' }}">{{ old("campos.$i.opciones", $opcionesTexto) }}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="ti ti-trash"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    Para <strong>Lista desplegable</strong>: una opción por línea en formato <code>valor|Etiqueta</code>.
                </div>
            </div>

            <div class="card">
                <div class="card-footer text-end">
                    <a href="{{ route('formatos.tipos.show', $tipo) }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let idx = {{ $tipo->campos->count() }};

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
        <td><input type="text" name="campos[${idx}][seccion]" class="form-control form-control-sm" placeholder="Ej: 2. Datos del Tema"></td>
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
