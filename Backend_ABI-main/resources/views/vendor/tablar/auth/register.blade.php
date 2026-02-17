@extends('tablar::auth.layout')
@section('title', 'Register')
@section('content')
    <div class="container container-tight py-4">
        <div class="text-center mb-1 mt-5">
            <a href="" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset(config('tablar.auth_logo.img.path', 'assets/logo.svg')) }}"
                     width="{{ config('tablar.auth_logo.img.width', 110) }}"
                     height="{{ config('tablar.auth_logo.img.height', 110) }}"
                     alt="{{ config('tablar.auth_logo.img.alt', 'Auth Logo') }}"
                     class="{{ trim('navbar-brand-image ' . config('tablar.auth_logo.img.class', '')) }}"
                     @if(config('tablar.auth_logo.img.style')) style="{{ config('tablar.auth_logo.img.style') }}" @endif>
            </a>
        </div>

        <form class="card card-md" action="{{ route('register') }}" method="post" autocomplete="off" novalidate>
            @csrf
            <div class="card-body">
                <!-- Success/error messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h2 class="card-title text-center mb-4">Registra un nuevo usuario</h2>

                <!-- Role Selection -->
                <div class="mb-3">
                    <label class="form-label">Rol del usuario</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Seleccione el rol --</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                        <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Docente</option>
                        <option value="committee_leader" {{ old('role') == 'committee_leader' ? 'selected' : '' }}>Líder de Comité</option>
                        <option value="research_staff" {{ old('role') == 'research_staff' ? 'selected' : '' }}>Personal de Investigación</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Personal Information (all required) -->
                <div class="mb-3">
                    <label class="form-label">Número de identificación</label>
                    <input type="text" name="card_id" class="form-control @error('card_id') is-invalid @enderror"
                           placeholder="Ingrese el número de identificación" value="{{ old('card_id') }}" required>
                    @error('card_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Ingrese el nombre" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                           placeholder="Ingrese el apellido" value="{{ old('last_name') }}" required>
                    @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           placeholder="Ingrese el número de teléfono" value="{{ old('phone') }}" required>
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student specific fields -->
                <div id="student-fields" class="role-fields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Semestre</label>
                        <input type="number" name="semester" class="form-control @error('semester') is-invalid @enderror"
                               placeholder="Ingrese el semestre (1-10)" min="1" max="10" value="{{ old('semester') }}" required>
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Common fields for professors, committee leaders and students -->
                <div id="program-fields" class="role-fields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Programa y ciudad</label>
                        <select name="city_program_id" class="form-select @error('city_program_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Programa --</option>
                            @foreach($cityPrograms as $program)
                                <option value="{{ $program->id }}" {{ old('city_program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_program_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email and Password -->
                <div class="mb-3">
                    <label class="form-label">Dirección de correo electrónico</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           placeholder="Ingrese el correo electrónico" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group input-group-flat">
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Contraseña" autocomplete="off" required>

                        <span class="input-group-text cursor-pointer pe-auto">
                            <a id="toggle-password" class="link-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7
                                            c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                </svg>
                            </a>
                        </span>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <div class="input-group input-group-flat">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="Confirmar contraseña" autocomplete="off" required>

                        <span class="input-group-text cursor-pointer pe-auto">
                            <a id="toggle-password-confirmation" class="link-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7
                                            c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                </svg>
                            </a>
                        </span>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit button -->
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Crear nuevo usuario</button>
                </div>
            </div>
        </form>
        <!-- Button to return to the user index -->
        <div class="text-center text-muted mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <polyline points="12 19 5 12 12 5" />
                </svg>
                Volver al listado de usuarios
            </a>
        </div>

    </div>

    <!-- JavaScript to show/hide fields based on role -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const studentFields = document.getElementById('student-fields');
            const programFields = document.getElementById('program-fields');

            function toggleFields() {
                const role = roleSelect.value;

                // Hide all dynamic fields
                studentFields.style.display = 'none';
                programFields.style.display = 'none';

                // Show relevant fields
                if (role === 'student') {
                    studentFields.style.display = 'block';
                    programFields.style.display = 'block';
                } else if (role === 'professor' || role === 'committee_leader') {
                    programFields.style.display = 'block';
                }
            }

            // Run on load
            toggleFields();

            // Listen to changes
            roleSelect.addEventListener('change', toggleFields);
        });

        document.addEventListener('DOMContentLoaded', function () {

            function togglePassword(inputId, toggleId) {
                const input = document.getElementById(inputId);
                const toggle = document.getElementById(toggleId);

                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            }

            togglePassword('password', 'toggle-password');
            togglePassword('password_confirmation', 'toggle-password-confirmation');
        });
    </script>
@endsection
