@extends('tablar::page')

@section('title', 'Formularios')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Formularios</h2>
                <p class="text-muted mb-0">Módulo en construcción.</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <p>Próximamente aquí podrás gestionar los formularios.</p>
            </div>
        </div>
    </div>
</div>
@endsection