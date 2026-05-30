@extends('tablar::page')

@section('title', 'Postular a Idea')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Postulación a: {{ $project->title }}</h2>
                    <p class="text-muted">Completa los siguientes campos para enviar tu solicitud.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('students.postulations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Información de la Postulación</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">Justificación</label>
                                    <textarea name="justification" class="form-control @error('justification') is-invalid @enderror" rows="6" placeholder="Explica tu interés, tus habilidades relacionadas y por qué eres un buen candidato para este proyecto.">{{ old('justification') }}</textarea>
                                    <small class="form-hint">Mínimo 50 caracteres.</small>
                                    @error('justification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Prioridad de la Postulación</label>
                                    <select name="priority_order" class="form-select @error('priority_order') is-invalid @enderror" required>
                                        <option value="">Selecciona la prioridad...</option>
                                        @foreach($availablePriorities as $value => $label)
                                            <option value="{{ $value }}" {{ old('priority_order') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-hint">Puedes tener hasta 3 postulaciones activas con diferentes niveles de prioridad.</small>
                                    @error('priority_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Historial de Notas (SINU)</label>
                                    <input type="file" name="grades_file" class="form-control @error('grades_file') is-invalid @enderror" accept="application/pdf">
                                    <small class="form-hint">Sube tu historial de notas en formato PDF (Max 2MB).</small>
                                    @error('grades_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-check">
                                        <input type="checkbox" name="accepted_terms" class="form-check-input @error('accepted_terms') is-invalid @enderror" value="1" {{ old('accepted_terms') ? 'checked' : '' }}>
                                        <span class="form-check-label required">He leído y acepto el tema propuesto y sus criterios de evaluación.</span>
                                    </label>
                                    @error('accepted_terms') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Modalidad e Integrantes</h3>
                                <div class="card-actions">
                                    <span class="badge bg-info-lt">Máximo {{ $maxStudents }} {{ Str::plural('integrante', $maxStudents) }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($maxStudents > 1)
                                    <div class="mb-4">
                                        <label class="form-label">¿Cómo planeas trabajar?</label>
                                        <div class="form-selectgroup">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="modality" value="individual" class="form-selectgroup-input" {{ old('modality', 'individual') === 'individual' ? 'checked' : '' }} onclick="toggleTeammates(false)">
                                                <span class="form-selectgroup-label">Individual</span>
                                            </label>
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="modality" value="team" class="form-selectgroup-input" {{ old('modality') === 'team' ? 'checked' : '' }} onclick="toggleTeammates(true)">
                                                <span class="form-selectgroup-label">En Equipo</span>
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="modality" value="individual">
                                    <div class="alert alert-info mb-3">
                                        Este proyecto está configurado para modalidad <strong>individual</strong>.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Integrante 1 (Líder)</label>
                                    <div class="row g-2">
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="{{ $student->full_name }} ({{ $student->card_id }})" disabled>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="lead_role" autocomplete="off" class="form-control @error('lead_role') is-invalid @enderror" placeholder="Tu Rol (ej. Coordinador, Desarrollador)" value="{{ old('lead_role') }}" required>
                                            @error('lead_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($maxStudents > 1)
                                    <div id="teammates-section" style="{{ old('modality') === 'team' ? '' : 'display: none;' }}">
                                        <div class="hr-text">Compañeros de Equipo</div>
                                        <p class="text-muted small mb-3">Puedes seleccionar hasta {{ $maxStudents - 1 }} {{ Str::plural('compañero', $maxStudents - 1) }} de tu mismo programa ({{ $student->cityProgram->program->name }}) y mismo semestre ({{ $student->semester }}°).</p>
                                        
                                        @for ($i = 0; $i < ($maxStudents - 1); $i++)
                                            <div class="mb-3">
                                                <label class="form-label">Integrante {{ $i + 2 }}</label>
                                                <div class="row g-2 teammate-row">
                                                    <div class="col-md-7">
                                                        <select name="teammates[{{ $i }}][id]" class="form-select teammate-select @error('teammates.'.$i.'.id') is-invalid @enderror">
                                                            <option value="">Opcional: Selecciona un compañero...</option>
                                                            @foreach ($availableStudents as $s)
                                                                <option value="{{ $s->id }}" {{ old("teammates.$i.id") == $s->id ? 'selected' : '' }}>
                                                                    {{ $s->name }} {{ $s->last_name }} ({{ $s->card_id }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('teammates.'.$i.'.id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" autocomplete="off"  name="teammates[{{ $i }}][role]" class="form-control @error('teammates.'.$i.'.role') is-invalid @enderror" placeholder="Rol del compañero"  value="{{ old("teammates.$i.role") }}">
                                                        @error('teammates.'.$i.'.role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Resumen de la Idea</h3>
                            </div>
                            <div class="card-body">
                                <h4 class="mb-1">{{ $project->title }}</h4>
                                <p class="text-muted small mb-3">{{ $project->thematicArea->name }}</p>
                                <div class="mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    Docente: {{ $project->professors->first()->name ?? 'N/A' }}
                                </div>
                                <hr>
                                <div class="d-flex flex-column gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        Enviar Postulación
                                    </button>
                                    <a href="{{ route('students.projects.approved.show', $project) }}" class="btn btn-link w-100 text-muted">Cancelar y Volver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleTeammates(show) {
            document.getElementById('teammates-section').style.display = show ? 'block' : 'none';
        }
    </script>
@endsection
