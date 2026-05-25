@extends('tablar::page')

@section('title', 'Nuevo Registro — ' . $tipo->nombre)

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.registros.index', $tipo) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">{{ $tipo->nombre }}</h2>
                <p class="text-muted mb-0">{{ $tipo->codigo }}</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form action="{{ route('formatos.registros.store', $tipo) }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Nuevo Registro</h3>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('formats.registros._form', ['tipo' => $tipo, 'valores' => []])

                </div>
            </div>

            <div class="card">
                <div class="card-footer text-end">
                    <a href="{{ route('formatos.registros.index', $tipo) }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Registro
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection
