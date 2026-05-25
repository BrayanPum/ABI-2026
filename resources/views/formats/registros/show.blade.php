@extends('tablar::page')

@section('title', $tipo->nombre . ' #' . $registro->id)

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
                <h2 class="page-title">{{ $tipo->nombre }} #{{ $registro->id }}</h2>
                <p class="text-muted mb-0">{{ $registro->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('formatos.registros.pdf', [$tipo, $registro]) }}"
                   class="btn btn-success me-2" target="_blank">
                    <i class="ti ti-file-download me-1"></i> Exportar PDF
                </a>
                <a href="{{ route('formatos.registros.edit', [$tipo, $registro]) }}"
                   class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @foreach ($tipo->campos->groupBy('seccion') as $seccion => $campos)

            <div class="card mb-3">
                @if ($seccion)
                    <div class="card-header">
                        <h3 class="card-title">{{ $seccion }}</h3>
                    </div>
                @endif
                <div class="card-body">
                    <dl class="row">
                        @foreach ($campos as $campo)
                            <dt class="col-sm-4">{{ $campo->etiqueta }}</dt>
                            <dd class="col-sm-8">
                                @php $valor = $valores[$campo->id] ?? null; @endphp

                                @if ($campo->tipo === 'checkbox')
                                    {{ $valor == '1' ? 'Sí' : 'No' }}
                                @elseif ($campo->tipo === 'select')
                                    @php
                                        $opcion = collect($campo->opciones ?? [])->firstWhere('valor', $valor);
                                    @endphp
                                    {{ $opcion ? $opcion['etiqueta'] : ($valor ?? '—') }}
                                @else
                                    {{ $valor ?? '—' }}
                                @endif
                            </dd>
                        @endforeach
                    </dl>
                </div>
            </div>

        @endforeach

        <div class="card">
            <div class="card-body text-muted">
                <small>
                    <strong>Registrado por:</strong> {{ $registro->user?->name ?? '—' }}
                    | <strong>Fecha:</strong> {{ $registro->created_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>

    </div>
</div>

@endsection
