@extends('tablar::page')
@section('title', 'Registrar ventana de calendario')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col"><h2 class="page-title">Registrar ventana de calendario</h2></div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('academic-process-windows.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('academic-process-windows.store') }}" novalidate>
                    @csrf
                    @include('academic-process-windows.form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
