{{--
    View path: perfil.blade.php.
    Purpose: Renders the perfil.blade view for the Perfil.Blade module.
    Expected variables within this template: $message.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
<!-- resources/views/perfil.blade.php -->

@extends('tablar::page')

@section('title', 'Perfil')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">{{ __('Perfil') }}</div>

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
                            <label for="name">{{ __('Nombre de Usuario') }}</label>
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
                            <label for="email">{{ __('Correo Electrónico') }}</label>
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
                            <label for="password">{{ __('Nueva Contraseña') }}</label>
                            {{-- Input element used to capture the 'password' value. --}}
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ __('La contraseña debe tener al menos 8 caracteres.') }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            {{-- Label describing the purpose of '{{ __('Confirmar Nueva Contraseña') }}'. --}}
                            <label for="password-confirm">{{ __('Confirmar Nueva Contraseña') }}</label>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
