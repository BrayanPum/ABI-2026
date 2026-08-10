@extends('tablar::auth.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Verifica tu correo electrónico</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        </div>
                    @endif

                    Antes de continuar, revisa tu correo electrónico para encontrar el enlace de verificación.
                    Si no recibiste el correo,
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">haz clic aquí para solicitar otro</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
