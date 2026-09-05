@extends('tablar::page')

@section('title', 'Editar perfil')

@section('content')
    @php
        $profileModel = $profile ?? null;
        $canManageFields = $canManageProfileFields ?? false;
        $currentName = old('name', $profileModel?->name ?? '');
        $currentLastName = old('last_name', $profileModel?->last_name ?? '');
        $currentEmail = old('email', $user?->email ?? '');
        $originalEmail = $user?->email ?? '';
        $currentPhone = old('phone', $profileModel?->phone ?? '');
        $emailConfirmation = old('email_confirmation', '');
    @endphp

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Configuracion personal</div>
                    <h2 class="page-title">{{ $canManageFields ? 'Editar perfil' : 'Cambiar contrasena' }}</h2>
                    <div class="text-muted">
                        {{ $canManageFields ? 'Actualiza la informacion principal de tu cuenta.' : 'Actualiza la contrasena de tu cuenta de forma segura.' }}
                    </div>
                </div>
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('perfil.show') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            Volver al perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">{{ $canManageFields ? 'Datos del perfil' : 'Seguridad de acceso' }}</h3>
                        </div>

                        <form method="POST" action="{{ route('perfil.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <div class="row g-3">
                                    @if ($canManageFields)
                                        <div class="col-12 col-md-6">
                                            <label for="name" class="form-label">Nombre</label>
                                            <input
                                                id="name"
                                                type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                name="name"
                                                value="{{ $currentName }}"
                                                required
                                                autofocus
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="last_name" class="form-label">Apellido</label>
                                            <input
                                                id="last_name"
                                                type="text"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                name="last_name"
                                                value="{{ $currentLastName }}"
                                                required
                                            >
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="email" class="form-label">Correo electronico</label>
                                            <input
                                                id="email"
                                                type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                name="email"
                                                value="{{ $currentEmail }}"
                                                data-original-email="{{ $originalEmail }}"
                                                required
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div
                                            class="col-12 {{ $emailConfirmation !== '' ? '' : 'd-none' }}"
                                            data-email-confirmation-group
                                        >
                                            <label for="email_confirmation" class="form-label">Confirmar nuevo correo</label>
                                            <input
                                                id="email_confirmation"
                                                type="email"
                                                class="form-control @error('email_confirmation') is-invalid @enderror"
                                                name="email_confirmation"
                                                value="{{ $emailConfirmation }}"
                                                data-email-confirmation-input
                                            >
                                            <div class="form-hint">Solo se solicita si cambias el correo actual.</div>
                                            @error('email_confirmation')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="phone" class="form-label">Telefono</label>
                                            <input
                                                id="phone"
                                                type="text"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                name="phone"
                                                value="{{ $currentPhone }}"
                                                required
                                                inputmode="numeric"
                                                pattern="[0-9]*"
                                                maxlength="20"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            >
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <div class="alert alert-info mb-0" role="alert">
                                                Ingresa una nueva contrasena, confirmala y usa al menos 9 caracteres. No se aceptan secuencias numericas obvias como 123456789.
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <hr class="my-1">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="password" class="form-label">Nueva contrasena</label>
                                        <input
                                            id="password"
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password"
                                            autocomplete="new-password"
                                            {{ $canManageFields ? '' : 'required' }}
                                        >
                                        <div class="form-hint">
                                            {{ $canManageFields ? 'Deja este campo vacio si no deseas cambiar la contrasena.' : 'Debe tener al menos 9 caracteres y no puede ser una secuencia numerica obvia.' }}
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div
                                        class="col-12 col-md-6 {{ old('password') ? '' : 'd-none' }}"
                                        data-password-confirmation-group
                                    >
                                        <label for="password_confirmation" class="form-label">Confirmar nueva contrasena</label>
                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation"
                                            autocomplete="new-password"
                                            data-password-confirmation-input
                                            {{ $canManageFields ? '' : 'required' }}
                                        >
                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent mt-auto">
                                <div class="btn-list justify-content-end">
                                    <a href="{{ route('perfil.show') }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $canManageFields ? 'Actualizar perfil' : 'Actualizar contrasena' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const emailInput = document.getElementById('email');
            const emailConfirmationGroup = document.querySelector('[data-email-confirmation-group]');
            const emailConfirmationInput = document.querySelector('[data-email-confirmation-input]');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationGroup = document.querySelector('[data-password-confirmation-group]');
            const passwordConfirmationInput = document.querySelector('[data-password-confirmation-input]');

            function syncEmailConfirmationVisibility() {
                if (!emailInput || !emailConfirmationGroup || !emailConfirmationInput) {
                    return;
                }

                const originalEmail = (emailInput.dataset.originalEmail || '').trim().toLowerCase();
                const currentEmail = (emailInput.value || '').trim().toLowerCase();
                const shouldShow = currentEmail !== '' && currentEmail !== originalEmail;

                emailConfirmationGroup.classList.toggle('d-none', !shouldShow);
                emailConfirmationInput.disabled = !shouldShow;

                if (!shouldShow) {
                    emailConfirmationInput.value = '';
                }
            }

            function syncPasswordConfirmationVisibility() {
                if (!passwordInput || !passwordConfirmationGroup || !passwordConfirmationInput) {
                    return;
                }

                const shouldShow = (passwordInput.value || '').trim() !== '';

                passwordConfirmationGroup.classList.toggle('d-none', !shouldShow);
                passwordConfirmationInput.disabled = !shouldShow;

                if (!shouldShow) {
                    passwordConfirmationInput.value = '';
                }
            }

            syncEmailConfirmationVisibility();
            syncPasswordConfirmationVisibility();

            emailInput?.addEventListener('input', syncEmailConfirmationVisibility);
            passwordInput?.addEventListener('input', syncPasswordConfirmationVisibility);
        });
    </script>
@endpush
