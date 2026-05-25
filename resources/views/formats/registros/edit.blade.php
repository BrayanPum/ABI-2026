@extends('tablar::page')

@section('title', 'Editar Registro #' . $registro->id . ' — ' . $tipo->nombre)

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <a href="{{ route('formatos.registros.show', [$tipo, $registro]) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="col">
                <h2 class="page-title">Editar Registro #{{ $registro->id }}</h2>
                <p class="text-muted mb-0">{{ $tipo->nombre }}</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <form action="{{ route('formatos.registros.update', [$tipo, $registro]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Editar Registro</h3>
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

                    @include('formats.registros._form', ['tipo' => $tipo, 'valores' => $valores])

                </div>
            </div>

            <div class="card">
                <div class="card-footer text-end">
                    <a href="{{ route('formatos.registros.show', [$tipo, $registro]) }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection
