@extends('tablar::page')

@section('title', 'Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <div class="card shadow-lg border border-secondary-subtle rounded-3">
                <div class="card-header bg-primary text-white text-center border-0 py-3">
                    <h4 class="m-0 text-white">{{ __('Perfil de Usuario') }}</h4>
                </div>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Cuenta personal</div>
                    <h2 class="page-title d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 1 8 0a4 4 0 0 1 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        Perfil de usuario
                    </h2>
                    <div class="text-muted">Consulta la informacion principal asociada a tu cuenta.</div>
                </div>
                @if (session('status'))
                    <div class="col-12">
                        <div class="alert alert-success mb-0" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-document">{{ __('Documento de identidad') }}</label>
                            <div id="perfil-readonly-document" class="perfil-readonly-field">{{ $userCard }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-name">{{ __('Nombre de Usuario') }}</label>
                            <div id="perfil-readonly-name" class="perfil-readonly-field">{{ $displayName }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-mail">{{ __('Correo Electrónico') }}</label>
                            <div id="perfil-readonly-mail" class="perfil-readonly-field">{{ $userMail }}</div>
                        </div>
                    </div>
                </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-phone">{{ __('Número telefónico') }}</label>
                            <div id="perfil-readonly-phone" class="perfil-readonly-field">{{ $userPhone }}</div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Nombre completo</dt>
                                <dd class="col-sm-7">{{ $displayName }}</dd>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-role">{{ __('Rol') }}</label>
                            <div id="perfil-readonly-role" class="perfil-readonly-field">{{ $nameUserRole }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-program">{{ __('Programa') }}</label>
                            <div id="perfil-readonly-program" class="perfil-readonly-field">{{ $userProgram }}</div>
                        </div>
                    </div>
                </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-body-secondary" for="perfil-readonly-city">{{ __('Ciudad') }}</label>
                            <div id="perfil-readonly-city" class="perfil-readonly-field">{{ $userCity }}</div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 text-muted">Documento</dt>
                                <dd class="col-sm-7">{{ $cardDisplay }}</dd>

                                <dt class="col-sm-5 text-muted">Telefono</dt>
                                <dd class="col-sm-7">{{ $phoneDisplay }}</dd>

                                @if ($showAcademicLocation)
                                    <dt class="col-sm-5 text-muted">Programa</dt>
                                    <dd class="col-sm-7">{{ $programDisplay }}</dd>

                                    <dt class="col-sm-5 text-muted">Ciudad</dt>
                                    <dd class="col-sm-7">{{ $cityDisplay }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .abi-profile-avatar-form {
            margin: 0;
        }

        .abi-profile-avatar-button {
            border: 0;
            padding: 0;
            background: transparent;
            cursor: pointer;
        }

        .abi-profile-avatar {
            overflow: hidden;
            position: relative;
            width: 96px;
            height: 96px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .abi-profile-avatar__image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: inherit;
        }

        .abi-profile-avatar__initials {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 1.5rem;
            line-height: 1;
            text-transform: uppercase;
        }

        .abi-profile-avatar__overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 600;
            color: #fff;
            background: rgba(15, 23, 42, 0.62);
            opacity: 0;
            transition: opacity 0.2s ease;
            border-radius: inherit;
        }

        .abi-profile-avatar-button:hover .abi-profile-avatar__overlay,
        .abi-profile-avatar-button:focus-visible .abi-profile-avatar__overlay {
            opacity: 1;
        }

        .abi-profile-avatar-button:focus-visible {
            outline: 2px solid var(--tblr-primary);
            outline-offset: 4px;
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const avatarTrigger = document.querySelector('[data-profile-photo-inline-trigger]');
            const avatarInput = document.querySelector('[data-profile-photo-inline-input]');

            avatarTrigger?.addEventListener('click', () => {
                avatarInput?.click();
            });

            avatarInput?.addEventListener('change', () => {
                if (avatarInput.files?.length) {
                    avatarInput.form?.submit();
                }
            });
        });
    </script>
@endpush
