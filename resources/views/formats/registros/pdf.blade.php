<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $tipo->nombre }} #{{ $registro->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 35px;
            line-height: 1.4;
        }

        .center { text-align: center; }

        .title {
            font-size: 15px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 12px;
            font-weight: bold;
        }

        .mb-20 { margin-bottom: 20px; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .section {
            background: #f7f7f7;
            font-weight: bold;
        }

        .label { font-weight: bold; width: 35%; }
    </style>
</head>
<body>

    <div class="center mb-20">
        <div class="title">{{ strtoupper($tipo->nombre) }}</div>
        @if ($tipo->codigo)
            <div class="subtitle">{{ $tipo->codigo }}</div>
        @endif
    </div>

    <table class="mb-20">

        @foreach ($tipo->campos->groupBy('seccion') as $seccion => $campos)

            @if ($seccion)
                <tr>
                    <td colspan="2" class="section">{{ $seccion }}</td>
                </tr>
            @endif

            @foreach ($campos as $campo)
                @php $valor = $valores[$campo->id] ?? null; @endphp
                <tr>
                    <td class="label">{{ $campo->etiqueta }}</td>
                    <td>
                        @if ($campo->tipo === 'checkbox')
                            {{ $valor == '1' ? 'Sí' : 'No' }}
                        @elseif ($campo->tipo === 'select')
                            @php $opcion = collect($campo->opciones ?? [])->firstWhere('valor', $valor); @endphp
                            {{ $opcion ? $opcion['etiqueta'] : ($valor ?? '—') }}
                        @else
                            {{ $valor ?? '—' }}
                        @endif
                    </td>
                </tr>
            @endforeach

        @endforeach

    </table>

    <p style="font-size:10px; color:#666;">
        Registrado por: {{ $registro->user?->name ?? '—' }} |
        Fecha: {{ $registro->created_at->format('d/m/Y H:i') }}
    </p>

</body>
</html>
