{{--
    View path: perfil.blade.php.
    Purpose: Renders the perfil.blade view for the Perfil.Blade module.
    Expected variables within this template: $message.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
<!-- resources/views/perfil.blade.php -->

@extends('tablar::page')

<<<<<<< Updated upstream
@section('title', 'Perfil')
=======
@php
    use App\Support\ProfileInputValidation;
@endphp

@section('title', 'Editar perfil')
>>>>>>> Stashed changes

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border border-secondary-subtle">
                <div class="card-header bg-primary text-white text-center border-0">{{ __('Perfil') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form method="POST" action="{{ route('perfil.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            {{-- Label describing the purpose of '{{ __('Nombre de Usuario') }}'. --}}
                            <label class="form-label fw-semibold text-body-secondary" for="name">{{ __('Nombre de Usuario') }}</label>
                            {{-- Input element used to capture the 'name' value. --}}
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ auth()->user()->name }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            {{-- Label describing the purpose of '{{ __('Correo Electrónico') }}'. --}}
                            <label class="form-label fw-semibold text-body-secondary" for="email">{{ __('Correo Electrónico') }}</label>
                            {{-- Input element used to capture the 'email' value. --}}
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ auth()->user()->email }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            {{-- Label describing the purpose of '{{ __('Nueva Contraseña') }}'. --}}
                            <label class="form-label fw-semibold text-body-secondary" for="password">{{ __('Nueva Contraseña') }}</label>
                            {{-- Input element used to capture the 'password' value. --}}
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ __('La contraseña debe tener al menos 8 caracteres.') }}</strong>
                                </span>
                            @enderror
                        </div>

<<<<<<< Updated upstream
                        <div class="form-group">
                            {{-- Label describing the purpose of '{{ __('Confirmar Nueva Contraseña') }}'. --}}
                            <label class="form-label fw-semibold text-body-secondary" for="password-confirm">{{ __('Confirmar Nueva Contraseña') }}</label>
                            {{-- Input element used to capture the 'password_confirmation' value. --}}
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="form-group mt-4">
                            {{-- Button element of type 'submit' to trigger the intended action. --}}
                            <button type="submit" class="btn btn-primary btn-block">
                                {{ __('Actualizar Perfil') }}
                            </button>
                        </div>
                    </form>
=======
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
                                                data-input-filter="name"
                                                pattern="{{ ProfileInputValidation::NAME_HTML_PATTERN }}"
                                                title="Solo letras, espacios, guiones y apostrofes."
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
                                                data-input-filter="name"
                                                pattern="{{ ProfileInputValidation::NAME_HTML_PATTERN }}"
                                                title="Solo letras, espacios, guiones y apostrofes."
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
                                                data-live-validate="email"
                                                autocomplete="email"
                                                required
                                            >
                                            <div
                                                class="invalid-feedback @error('email') d-block @else d-none @enderror"
                                                data-live-email-feedback
                                            >
                                                @error('email')
                                                    {{ $message }}
                                                @else
                                                    Ingrese un correo electronico valido (ejemplo: usuario@dominio.com).
                                                @enderror
                                            </div>
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
                                                data-live-validate="email"
                                                autocomplete="email"
                                            >
                                            <div class="form-hint">Solo se solicita si cambias el correo actual.</div>
                                            <div
                                                class="invalid-feedback @error('email_confirmation') d-block @else d-none @enderror"
                                                data-live-email-feedback
                                            >
                                                @error('email_confirmation')
                                                    {{ $message }}
                                                @else
                                                    Ingrese un correo electronico valido (ejemplo: usuario@dominio.com).
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="phone" class="form-label">Telefono</label>
                                            <input
                                                id="phone"
                                                type="text"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                name="phone"
                                                value="{{ $currentPhone }}"
                                                data-input-filter="phone-co"
                                                data-live-validate="phone-co"
                                                inputmode="numeric"
                                                maxlength="{{ ProfileInputValidation::PHONE_LENGTH }}"
                                                minlength="{{ ProfileInputValidation::PHONE_LENGTH }}"
                                                pattern="{{ ProfileInputValidation::PHONE_HTML_PATTERN }}"
                                                placeholder="3158899001"
                                                title="Debe tener 10 digitos."
                                                required
                                            >
                                            <div
                                                class="invalid-feedback @error('phone') d-block @else d-none @enderror"
                                                data-live-phone-feedback
                                            >
                                                @error('phone')
                                                    {{ $message }}
                                                @else
                                                    El telefono debe tener exactamente 10 digitos (ejemplo: 3158899001).
                                                @enderror
                                            </div>
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
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<<<<<<< Updated upstream
=======

@push('js')
    @include('partials.user-profile-input-filters')

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
>>>>>>> Stashed changes
