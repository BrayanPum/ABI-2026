@extends('tablar::page')

@section('title', 'Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="m-0">{{ __('Perfil de Usuario') }}</h4>
                </div>

                <div class="card-body p-4">
                    
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Documento de identidad</label>
                            <div class="form-control bg-light">{{ $userCard }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Nombre de Usuario</label>
                            <div class="form-control bg-light">{{ $displayName }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Correo Electrónico</label>
                            <div class="form-control bg-light">{{ $userMail }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Número telefónico</label>
                            <div class="form-control bg-light">{{ $userPhone }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Rol</label>
                            <div class="form-control bg-light">{{ $nameUserRole }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Programa</label>
                            <div class="form-control bg-light">{{ $userProgram }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-primary">Ciudad</label>
                            <div class="form-control bg-light">{{ $userCity }}</div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
