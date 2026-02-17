{{--
    Partial path: users/form.blade.php.
    Purpose: Shared form fragment for creating and editing users with a layout aligned with the
    projects catalogue.
    Expected variables:
    - $user (optional): current user model when editing.
    - $details (optional): profile details associated to the user.
    - $cityPrograms (optional): collection of city-program combinations.
--}}
@php
    $userModel = $user ?? null;
    $detailsModel = $details ?? null;
    $selectedRole = old('role', $userModel->role ?? 'student');
    $isExistingUser = isset($userModel) && $userModel->exists;
@endphp

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <label for="email" class="form-label required">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $userModel->email ?? '') }}"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <label for="state" class="form-label required">Estado</label>
        <select id="state" name="state" class="form-select @error('state') is-invalid @enderror" required>
            <option value="1" {{ old('state', $userModel->state ?? '1') == '1' ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ old('state', $userModel->state ?? '1') == '0' ? 'selected' : '' }}>Inactivo</option>
        </select>
        @error('state')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <label for="role" class="form-label required">Rol</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="student" {{ $selectedRole === 'student' ? 'selected' : '' }}>Estudiante</option>
            <option value="professor" {{ $selectedRole === 'professor' ? 'selected' : '' }}>Profesor</option>
            <option value="committee_leader" {{ $selectedRole === 'committee_leader' ? 'selected' : '' }}>Líder de Comité</option>
            <option value="research_staff" {{ $selectedRole === 'research_staff' ? 'selected' : '' }}>Personal de Investigación</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-6">
        <label for="password" class="form-label{{ $isExistingUser ? '' : ' required' }}">{{ $isExistingUser ? 'Nueva contraseña' : 'Contraseña' }}</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            {{ $isExistingUser ? '' : 'required' }}
            autocomplete="new-password"
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">{{ $isExistingUser ? 'Deja en blanco para mantener la contraseña actual.' : 'Debe tener al menos 8 caracteres.' }}</small>
    </div>
    <div class="col-12 col-lg-6">
        <label for="password_confirmation" class="form-label{{ $isExistingUser ? '' : ' required' }}">Confirmar contraseña</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            {{ $isExistingUser ? '' : 'required' }}
            autocomplete="new-password"
        >
        @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="card_id" class="form-label required">Cédula</label>
        @if($isExistingUser)
            <div class="input-group">
                <input type="text" class="form-control" value="{{ old('card_id', $detailsModel->card_id ?? '') }}" readonly>
                <input type="hidden" id="card_id" name="card_id" value="{{ old('card_id', $detailsModel->card_id ?? '') }}">
            </div>
        @else
            <input
                type="text"
                id="card_id"
                name="card_id"
                class="form-control @error('card_id') is-invalid @enderror"
                value="{{ old('card_id') }}"
                required
                autocomplete="off"
            >
        @endif
        @error('card_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label for="phone" class="form-label required">Teléfono</label>
        <input
            type="text"
            id="phone"
            name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $detailsModel->phone ?? '') }}"
            required
        >
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="name" class="form-label required">Nombre</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $detailsModel->name ?? '') }}"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label for="last_name" class="form-label required">Apellido</label>
        <input
            type="text"
            id="last_name"
            name="last_name"
            class="form-control @error('last_name') is-invalid @enderror"
            value="{{ old('last_name', $detailsModel->last_name ?? '') }}"
            required
        >
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-6" data-role-visible="student,professor,committee_leader">
        <label for="city_program_id" class="form-label required">Programa académico</label>
        <select id="city_program_id" name="city_program_id" class="form-select @error('city_program_id') is-invalid @enderror">
            <option value="">Selecciona una opción</option>
            @foreach(($cityPrograms ?? []) as $program)
                <option value="{{ $program->id }}" {{ (string)old('city_program_id', $detailsModel->city_program_id ?? '') === (string)$program->id ? 'selected' : '' }}>
                    {{ $program->full_name ?? ($program->program->name ?? 'Programa') . ' - ' . ($program->city->name ?? 'Ciudad') }}
                </option>
            @endforeach
        </select>
        @error('city_program_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-lg-6" data-role-visible="student">
        <label for="semester" class="form-label required">Semestre</label>
        <input
            type="number"
            id="semester"
            name="semester"
            min="1"
            max="10"
            class="form-control @error('semester') is-invalid @enderror"
            value="{{ old('semester', $detailsModel->semester ?? '') }}"
        >
        @error('semester')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-hint">Disponible solo para estudiantes.</small>
    </div>
    @if(in_array($selectedRole, ['professor', 'committee_leader']))
        <input type="hidden" name="committee_leader" value="{{ $selectedRole === 'committee_leader' ? 1 : 0 }}">
    @endif


</div>

@once
    @push('css')
        <style>
            .form-label.required::after {
                content: ' *';
                color: var(--tblr-danger);
            }
        </style>
    @endpush

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const roleSelect = document.getElementById('role');
                const toggledFields = document.querySelectorAll('[data-role-visible]');

                function updateRoleFields() {
                    const currentRole = roleSelect?.value ?? '';
                    toggledFields.forEach((element) => {
                        const visibleFor = (element.getAttribute('data-role-visible') || '').split(',');
                        const shouldShow = visibleFor.map(value => value.trim()).includes(currentRole);
                        element.classList.toggle('d-none', !shouldShow);
                        const inputs = element.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            if (shouldShow) {
                                input.removeAttribute('disabled');
                            } else {
                                input.setAttribute('disabled', 'disabled');
                            }
                        });
                    });
                }

                updateRoleFields();
                roleSelect?.addEventListener('change', updateRoleFields);
            });
        </script>
    @endpush
@endonce
