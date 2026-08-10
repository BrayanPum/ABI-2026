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
                    <form class="card" action="{{ route('password.email') }}" method="post" novalidate>
                        @csrf
                        <div class="card-body p-6">
                            <div class="card-title">¿Olvidaste tu contraseña?</div>

                            <p class="text-muted">Ingresa tu correo electrónico.</p>
                            <div class="form-group">
                                <label class="form-label" for="exampleInputEmail1">Correo electrónico</label>
                                <input
                                    type="email"
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    id="email"
                                    name="email"
                                    aria-describedby="emailHelp"
                                    placeholder="Ingresa tu correo"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-footer">
                                <button type="submit"
                                        class="btn btn-primary btn-block">Enviar enlace de restablecimiento</button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center text-muted">
                        <a href="{{ route('login') }}">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
