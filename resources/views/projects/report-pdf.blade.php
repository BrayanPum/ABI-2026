<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            margin: 120px 34px 54px 34px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.45;
        }

        .pdf-header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 82px;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
        }

        .pdf-footer {
            position: fixed;
            bottom: -32px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
            text-align: right;
        }

        .header-table,
        .summary-table,
        .mini-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo {
            width: 82px;
            vertical-align: top;
        }

        .header-logo img {
            width: 64px;
            height: auto;
        }

        .header-main {
            vertical-align: top;
        }

        .header-main h1 {
            margin: 0;
            font-size: 22px;
            color: #0f172a;
        }

        .header-main .institution {
            margin-top: 4px;
            font-size: 13px;
            color: #0f766e;
            font-weight: bold;
        }

        .header-main .system-name {
            margin-top: 2px;
            color: #475569;
            font-size: 11px;
        }

        .header-meta {
            width: 220px;
            text-align: right;
            vertical-align: top;
            font-size: 10px;
            color: #475569;
        }

        .header-meta .meta-label {
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-size: 9px;
        }

        .page-title {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #0f172a;
        }

        .page-description {
            margin: 0 0 16px 0;
            color: #475569;
        }

        .filter-box,
        .section-box {
            border: 1px solid #dbe4ea;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
            background: #ffffff;
        }

        .filter-box {
            background: #f8fafc;
        }

        .section-title {
            margin: 0 0 6px 0;
            font-size: 14px;
            color: #0f172a;
        }

        .section-description {
            margin: 0 0 12px 0;
            color: #64748b;
            font-size: 10px;
        }

        .chip {
            display: inline-block;
            margin: 0 8px 8px 0;
            padding: 5px 9px;
            border-radius: 999px;
            background: #e6fffb;
            color: #115e59;
            font-size: 10px;
            border: 1px solid #99f6e4;
        }

        .summary-table td {
            width: 25%;
            vertical-align: top;
            padding-right: 10px;
        }

        .summary-card {
            border: 1px solid #dbe4ea;
            border-radius: 12px;
            padding: 12px;
            background: #ffffff;
            min-height: 78px;
        }

        .summary-card .label {
            margin: 0 0 6px 0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .summary-card .value {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-card .caption {
            margin: 6px 0 0 0;
            font-size: 9px;
            color: #6b7280;
        }

        .mini-table td {
            width: 33.33%;
            padding-right: 10px;
            vertical-align: top;
        }

        .mini-metric {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .mini-metric .metric-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .mini-metric .metric-value {
            margin-top: 5px;
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }

        .data-table {
            margin-top: 8px;
        }

        .data-table thead th {
            padding: 9px 8px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            background: #f1f5f9;
            border-bottom: 1px solid #dbe4ea;
            text-align: left;
        }

        .data-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) {
            background: #fcfdff;
        }

        .value-cell,
        .percentage-cell {
            text-align: right;
            white-space: nowrap;
        }

        .bar-track {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
            margin-top: 4px;
        }

        .bar-fill {
            height: 10px;
            border-radius: 999px;
        }

        .muted {
            color: #6b7280;
        }

        .empty-state {
            padding: 18px 0;
            text-align: center;
            color: #6b7280;
        }

        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="pdf-header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if ($logoDataUri)
                        <img src="{{ $logoDataUri }}" alt="UDI logo">
                    @endif
                </td>
                <td class="header-main">
                    <h1>{{ $reportSubject ?? 'Reporte de proyectos' }}</h1>
                    <div class="institution">Universidad de Investigacion y Desarrollo UDI</div>
                    <div class="system-name">ABI Sistema de gestion</div>
                </td>
                <td class="header-meta">
                    <span class="meta-label">Fecha de generacion</span>
                    {{ $reportGeneratedAt->format('d/m/Y H:i') }}<br>
                    <span class="meta-label" style="margin-top: 8px;">Reporte</span>
                    {{ $reportTitle }}
                </td>
            </tr>
        </table>
    </div>

    <div class="pdf-footer">
        Documento generado automaticamente por ABI Sistema de gestion
    </div>

    <h2 class="page-title">{{ $reportTitle }}</h2>
    <p class="page-description">{{ $reportDescription }}</p>

    <div class="filter-box page-break-avoid">
        <h3 class="section-title">Filtros aplicados</h3>
        @foreach ($reportFiltersSummary as $filterItem)
            <span class="chip">{{ $filterItem }}</span>
        @endforeach
    </div>

    <div class="section-box page-break-avoid">
        <h3 class="section-title">Resumen ejecutivo</h3>
        <table class="summary-table">
            <tr>
                @foreach ($reportInsights as $insight)
                    <td>
                        <div class="summary-card">
                            <div class="label">{{ $insight['label'] }}</div>
                            <p class="value">{{ $insight['value'] }}</p>
                            <p class="caption">{{ $insight['caption'] }}</p>
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    @foreach ($reportVisuals as $visual)
        @php
            $visualData = $visual['data'] ?? ['categories' => [], 'values' => [], 'percentages' => [], 'total' => 0];
            $visualSegments = $visual['segments'] ?? [];
            $visualMaxValue = max($visualData['values'] ?: [0]);
        @endphp

        <div class="section-box page-break-avoid">
            <h3 class="section-title">{{ $visual['title'] }}</h3>
            <p class="section-description">{{ $visual['description'] }}</p>

            <table class="mini-table">
                <tr>
                    <td>
                        <div class="mini-metric">
                            <div class="metric-label">{{ $visual['total_label'] }}</div>
                            <div class="metric-value">{{ $visualData['total'] }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="mini-metric">
                            <div class="metric-label">Categorias</div>
                            <div class="metric-value">{{ count($visualData['categories']) }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="mini-metric">
                            <div class="metric-label">Valor principal</div>
                            <div class="metric-value">{{ $visualSegments[0]['value'] ?? 0 }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            @if ($visualSegments !== [])
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 34%;">Categoria</th>
                            <th style="width: 10%;">Valor</th>
                            <th style="width: 12%;">Porcentaje</th>
                            <th style="width: 44%;">Representacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visualSegments as $segment)
                            @php
                                $barWidth = $visualMaxValue > 0 ? round(($segment['value'] / $visualMaxValue) * 100, 2) : 0;
                            @endphp
                            <tr>
                                <td>{{ $segment['label'] }}</td>
                                <td class="value-cell">{{ $segment['value'] }}</td>
                                <td class="percentage-cell">{{ number_format($segment['percentage'], 2) }}%</td>
                                <td>
                                    <div class="muted">{{ number_format($segment['percentage'], 2) }}%</div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: {{ $barWidth }}%; background: {{ $segment['color'] }};"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">No se encontraron datos para esta visualizacion.</div>
            @endif
        </div>
    @endforeach

    @if ($reportTable)
        <div class="section-box">
            <h3 class="section-title">{{ $reportTable['title'] }}</h3>
            <p class="section-description">{{ $reportTable['description'] }}</p>

            <table class="data-table">
                <thead>
                    <tr>
                        @foreach ($reportTable['columns'] as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportTable['rows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($reportTable['columns']) }}" class="empty-state">
                                No se encontraron registros para este reporte.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
