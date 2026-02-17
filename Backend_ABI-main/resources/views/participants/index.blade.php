@extends('tablar::page')

@section('title', 'Participantes')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Participantes</h2>
                <p class="text-muted mb-0">Listado de docentes y líderes de comité disponibles.</p>
            </div>
        </div>
    </div>
    </div>

<div class="page-body">
    <div class="container-xl">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form class="row g-3 align-items-end" id="filters-form">
                    <div class="col-12 col-md-4">
                        <label for="q" class="form-label">Buscar</label>
                        <input id="q" type="search" class="form-control" placeholder="Nombre, documento o correo">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="program_id" class="form-label">Programa</label>
                        <select id="program_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="reset" class="btn btn-outline-secondary w-100">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
                <div class="card-actions">
                    <span class="badge bg-azure" id="total">0</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Documento</th>
                            <th>Correo</th>
                            <th>Programa</th>
                            <th>Ciudad</th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const endpoint = '{{ route('projects.participants') }}';
    const rows = document.getElementById('rows');
    const total = document.getElementById('total');
    const q = document.getElementById('q');
    const program = document.getElementById('program_id');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function load() {
        rows.innerHTML = '<tr><td colspan="6" class="text-center text-secondary py-4">Cargando...</td></tr>';
        const params = new URLSearchParams();
        if (q.value.trim()) params.set('q', q.value.trim());
        if (program.value) params.set('program_id', program.value);
        const url = `${endpoint}?${params.toString()}`;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) {
            rows.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">No se pudo cargar la lista.</td></tr>';
            total.textContent = '0';
            return;
        }
        const data = await response.json();
        const items = Array.isArray(data?.data) ? data.data : [];
        total.textContent = items.length;
        if (!items.length) {
            rows.innerHTML = '<tr><td colspan="6" class="text-center text-secondary py-4">Sin resultados.</td></tr>';
            return;
        }
        rows.innerHTML = items.map((p, i) => `
            <tr>
                <td class="text-muted">${i + 1}</td>
                <td>${escapeHtml(p.name)}</td>
                <td>${escapeHtml(p.document ?? '—')}</td>
                <td>${escapeHtml(p.email ?? '—')}</td>
                <td>${escapeHtml(p.program ?? '—')}</td>
                <td>${escapeHtml(p.program_city ?? '—')}</td>
            </tr>
        `).join('');
    }

    document.getElementById('filters-form').addEventListener('reset', () => setTimeout(load, 0));
    q.addEventListener('input', () => load());
    program.addEventListener('change', () => load());
    load();
});
</script>
@endpush