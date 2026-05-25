@php
    $visualSegments = $visual['segments'] ?? [];
    $visualData = $visual['data'] ?? ['categories' => [], 'values' => [], 'percentages' => [], 'total' => 0];
    $valueLabel = $visual['value_label'] ?? 'registros';
    $maxVisualValue = max($visualData['values'] ?: [0]);
    $currentPercent = 0;
    $chartStops = [];

    foreach ($visualSegments as $segment) {
        $start = $currentPercent;
        $currentPercent = min(100, $currentPercent + $segment['percentage']);
        $chartStops[] = "{$segment['color']} {$start}% {$currentPercent}%";
    }

    $chartBackground = $chartStops !== []
        ? 'conic-gradient(' . implode(', ', $chartStops) . ')'
        : 'linear-gradient(135deg, #d1d5db, #9ca3af)';
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div>
            <h4 class="card-title mb-0">{{ $visual['title'] }}</h4>
            <div class="text-muted">{{ $visual['description'] }}</div>
        </div>
    </div>
    <div class="card-body project-report-shell">
        <div class="project-report-toolbar">
            <div class="project-report-switch" role="tablist" aria-label="Tipos de grafico del reporte">
                <button type="button" class="project-report-switch__button is-active" data-chart-group="{{ $groupId }}" data-chart-target="donut">Dona</button>
                <button type="button" class="project-report-switch__button" data-chart-group="{{ $groupId }}" data-chart-target="columns">Barras verticales</button>
                <button type="button" class="project-report-switch__button" data-chart-group="{{ $groupId }}" data-chart-target="rows">Barras horizontales</button>
            </div>
            <div class="text-muted small">Cambia la visualizacion sin perder los filtros actuales.</div>
        </div>

        <div class="project-report-visual">
            <div class="project-report-panel-wrap">
                <div class="project-report-panel is-active" data-chart-group="{{ $groupId }}" data-chart-panel="donut">
                    @if ($visualSegments !== [])
                        <div class="project-report-donut-wrap">
                            <div class="project-report-donut" style="background: {{ $chartBackground }};">
                                <div class="project-report-donut__center">
                                    <div>
                                        <strong>{{ $visualData['total'] }}</strong>
                                        <span>{{ $visual['total_label'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted text-center">
                                Diagrama de dona generado con los filtros seleccionados.
                            </div>
                        </div>
                    @else
                        <div class="project-report-empty">Sin datos para construir el grafico.</div>
                    @endif
                </div>

                <div class="project-report-panel" data-chart-group="{{ $groupId }}" data-chart-panel="columns">
                    @if ($visualSegments !== [])
                        <div class="project-report-columns">
                            @foreach ($visualSegments as $segment)
                                @php
                                    $columnHeight = $maxVisualValue > 0
                                        ? max(14, (int) round(($segment['value'] / $maxVisualValue) * 220))
                                        : 14;
                                @endphp
                                <div class="project-report-column">
                                    <div class="project-report-column__value">{{ $segment['value'] }}</div>
                                    <div
                                        class="project-report-column__bar"
                                        style="height: {{ $columnHeight }}px; background: {{ $segment['color'] }};"
                                        title="{{ $segment['label'] }}: {{ $segment['value'] }}"
                                    >
                                        {{ number_format($segment['percentage'], 1) }}%
                                    </div>
                                    <div class="project-report-column__label">{{ $segment['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="project-report-empty">Sin datos para construir el grafico.</div>
                    @endif
                </div>

                <div class="project-report-panel" data-chart-group="{{ $groupId }}" data-chart-panel="rows">
                    @if ($visualSegments !== [])
                        <div class="project-report-rows">
                            @foreach ($visualSegments as $segment)
                                @php
                                    $rowWidth = $maxVisualValue > 0
                                        ? round(($segment['value'] / $maxVisualValue) * 100, 2)
                                        : 0;
                                @endphp
                                <div class="project-report-row">
                                    <div class="project-report-row__header">
                                        <span>{{ $segment['label'] }}</span>
                                        <span>{{ $segment['value'] }} {{ $valueLabel }}</span>
                                    </div>
                                    <div class="project-report-row__track">
                                        <div
                                            class="project-report-row__fill"
                                            style="width: {{ $rowWidth }}%; background: {{ $segment['color'] }};"
                                            title="{{ $segment['label'] }}: {{ number_format($segment['percentage'], 2) }}%"
                                        ></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="project-report-empty">Sin datos para construir el grafico.</div>
                    @endif
                </div>
            </div>

            <div class="project-report-legend">
                @forelse ($visualSegments as $segment)
                    <div class="project-report-legend__item">
                        <span class="project-report-legend__swatch" style="background: {{ $segment['color'] }}"></span>
                        <div>
                            <div class="fw-semibold">{{ $segment['label'] }}</div>
                            <div class="text-muted small">{{ $segment['value'] }} {{ $valueLabel }}</div>
                        </div>
                        <div class="fw-semibold">{{ number_format($segment['percentage'], 2) }}%</div>
                    </div>
                @empty
                    <div class="text-muted">Sin datos para construir la leyenda del reporte.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
