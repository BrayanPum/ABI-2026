@extends('tablar::auth.layout')

@section('content')
    <div class="page-single">
        <div class="container">
            <div class="row">
                <div class="col col-login mx-auto">
                    <div class="text-center mb-1 mt-5">
                        <a href="" class="navbar-brand navbar-brand-autodark">
                            <img src="{{ asset(config('tablar.auth_logo.img.path', 'assets/tablar-logo.png')) }}" height="36"
                                 alt="Logo de autenticación"></a>
                    </div>
                    <form class="card" method="POST" action="{{ route('password.request') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="card-body p-6">
                            <div class="card-title">Restablecer contraseña</div>

                            <p class="text-muted">Ingresa tu correo electrónico y define una nueva contraseña.</p>
                            <div class="form-group">
                                <label class="form-label" for="exampleInputEmail1">Correo electrónico</label>
                                <input
                                    type="email"
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    id="email"
                                    name="email"
                                    aria-describedby="emailHelp"
                                    placeholder="Ingresa tu correo"
                                    value="{{ $email ?? old('email') }}"
                                    required
                                    autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label class="form-label">Contraseña</label>
                                <input
                                    type="password"
                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    placeholder="Contraseña"
                                    name="password"
                                    required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password-confirm">Confirmar contraseña</label>
                                <input
                                    type="password"
                                    class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                    placeholder="Confirmar contraseña"
                                    name="password_confirmation"
                                    id="password-confirm">
                                @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary btn-block">Restablecer contraseña</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
