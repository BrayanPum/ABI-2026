@extends('tablar::auth.layout')
@section('title', 'Login')
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
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Ingresa tu cuenta</h2>
                
                <!-- Display Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Cuenta email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                               placeholder="your@email.com" autocomplete="off" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Contraseña
                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Your password" autocomplete="off" required>
                            <span class="input-group-text">
                                <a href="#" class="link-secondary pe-auto" title="Mostrar contraseña" data-bs-toggle="tooltip" id="toggle-password">
                                    <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>

                                    <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" class="icon d-none" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3l18 18" />
                                        <path d="M10.584 10.587a2 2 0 1 0 2.829 2.828" />
                                        <path d="M9.49 5.51a9 9 0 0 1 10.7 6.49a11.91 11.91 0 0 1 -1.664 3.218" />
                                        <path d="M6.53 6.53a11.91 11.91 0 0 0 -3.218 5.47a9 9 0 0 0 9 6c1.66 0 3.22 -.43 4.6 -1.2" />
                                    </svg>
                                </a>
                            </span>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const togglePassword = document.getElementById('toggle-password');
            const passwordField = document.getElementById('password');

            const iconEye = document.getElementById('icon-eye');
            const iconEyeOff = document.getElementById('icon-eye-off');

            togglePassword.addEventListener('click', function(e) {
                e.preventDefault();

                // Alternar tipo de campo
                const isHidden = passwordField.getAttribute('type') === 'password';
                passwordField.setAttribute('type', isHidden ? 'text' : 'password');

                // Cambiar iconos
                iconEye.classList.toggle('d-none', !isHidden);
                iconEyeOff.classList.toggle('d-none', isHidden);

                // Cambiar tooltip
                togglePassword.setAttribute('title', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });
        </script>
@endsection
