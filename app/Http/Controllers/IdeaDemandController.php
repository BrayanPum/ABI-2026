<?php

namespace App\Http\Controllers;

use App\Models\AcademicProcessWindow;
use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Services\AcademicCalendar\AcademicCalendarService;
use App\Services\Projections\IdeaDemandProjectionService;
use App\Services\Projections\ProjectionPeriodService;
use App\Services\Reports\VisualReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IdeaDemandController extends Controller
{
    public function __construct(
        private readonly ProjectionPeriodService $periods,
        private readonly IdeaDemandProjectionService $demand,
        private readonly VisualReportService $reports,
    ) {
    }

    public function index(Request $request): View|Response
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_IDEA_DEMAND_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_IDEA_DEMAND_PROJECTION);
        }

        $periods = $this->periods->allPeriods();
        $targetPeriod = $this->periods->targetPeriod();
        $selectedPeriodId = $request->integer('academic_period_id') ?: $targetPeriod?->id ?: $periods->first()?->id;
        $selectedProgramId = $request->integer('program_id');
        $summary = $this->demand->summaryForPeriod($selectedPeriodId, $selectedProgramId);
        $programs = ResearchStaffProgram::uniqueOptions();
        $selectedProgramIds = ResearchStaffProgram::equivalentIds($selectedProgramId);
        $detailRow = $selectedProgramId
            ? $summary['rows']->first(fn ($row) => in_array((int) $row['program']?->id, $selectedProgramIds, true))
            : ($summary['rows']->count() === 1 ? $summary['rows']->first() : null);
        $reportState = $this->buildIdeaDemandReportState(
            $request,
            $summary['rows'],
            $periods,
            $programs,
            $selectedPeriodId,
            $selectedProgramId
        );

        if ($reportState['export'] === 'pdf') {
            return $this->reports->downloadPdf(
                (string) $reportState['reportKey'],
                (string) $reportState['reportLabel'],
                $this->ideaDemandReportModules()[$reportState['reportKey']]['description'] ?? 'Resumen de demanda de ideas.',
                $reportState['insights'],
                $reportState['visuals'],
                $reportState['table'],
                $this->ideaDemandReportFiltersSummary($periods, $programs, $selectedPeriodId, $selectedProgramId),
                'Reporte de demanda de ideas',
                'demanda-ideas'
            );
        }

        return view('projections.idea-demand.index', [
            'periods' => $periods,
            'programs' => $programs,
            'targetPeriod' => $targetPeriod,
            'selectedPeriodId' => $selectedPeriodId,
            'selectedProgramId' => $selectedProgramId,
            'summary' => $summary,
            'detailRow' => $detailRow,
            'reportModules' => $this->ideaDemandReportModules(),
            'reportFilters' => $reportState['filters'],
            'reportData' => $reportState['reportData'],
            'reportInsights' => $reportState['insights'],
            'reportVisuals' => $reportState['visuals'],
            'reportTable' => $reportState['table'],
            'activeReportKey' => $reportState['reportKey'],
        ]);
    }

    /**
     * @return array<string, array{label: string, description: string}>
     */
    protected function ideaDemandReportModules(): array
    {
        return [
            'demand_vs_supply_by_program' => [
                'label' => 'Demanda vs banco',
                'description' => 'Compara la demanda proyectada con las ideas aprobadas y sin asignar disponibles por programa.',
            ],
            'coverage_status' => [
                'label' => 'Cobertura por programa',
                'description' => 'Clasifica los programas segun si su banco cubre, excede o queda corto frente a la demanda.',
            ],
            'thematic_bank_balance' => [
                'label' => 'Balance tematico',
                'description' => 'Resume en que lineas y areas tematicas se concentra el banco disponible del periodo filtrado.',
            ],
        ];
    }

    /**
     * @return array{
     *     filters: array{report_key:string,academic_period_id:?int,program_id:?int},
     *     reportKey: string,
     *     reportLabel: string,
     *     reportData: array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int},
     *     insights: array<int, array{label: string, value: string, caption: string}>,
     *     visuals: array<int, array<string, mixed>>,
     *     table: ?array{title: string, description: string, columns: array<int, string>, rows: array<int, array<int, string>>},
     *     export: ?string
     * }
     */
    protected function buildIdeaDemandReportState(
        Request $request,
        Collection $rows,
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId
    ): array {
        $filters = $request->validate([
            'report_key' => ['nullable', Rule::in(array_keys($this->ideaDemandReportModules()))],
            'report_export' => ['nullable', Rule::in(['pdf'])],
        ]);

        $reportKey = $filters['report_key'] ?? 'demand_vs_supply_by_program';
        $reportState = $this->composeIdeaDemandReportState($reportKey, $rows);

        return [
            'filters' => [
                'report_key' => $reportKey,
                'academic_period_id' => $selectedPeriodId,
                'program_id' => $selectedProgramId,
            ],
            'reportKey' => $reportKey,
            'reportLabel' => $this->ideaDemandReportModules()[$reportKey]['label'] ?? 'Reporte de demanda de ideas',
            'reportData' => $reportState['reportData'],
            'insights' => $reportState['insights'],
            'visuals' => $reportState['visuals'],
            'table' => $reportState['table'],
            'export' => $filters['report_export'] ?? null,
        ];
    }

    protected function composeIdeaDemandReportState(string $reportKey, Collection $rows): array
    {
        return match ($reportKey) {
            'coverage_status' => $this->buildIdeaCoverageStatusReportState($rows),
            'thematic_bank_balance' => $this->buildIdeaThematicBalanceReportState($rows),
            default => $this->buildIdeaDemandVsSupplyReportState($rows),
        };
    }

    protected function buildIdeaDemandVsSupplyReportState(Collection $rows): array
    {
        $neededIdeas = $rows->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['needed_ideas']])->sortDesc();
        $availableIdeas = $rows->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['available_ideas']])->sortDesc();
        $missingIdeas = $rows->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['missing_ideas']])->sortDesc();
        $coveragePrograms = $rows->filter(fn (array $row) => (int) $row['missing_ideas'] === 0)->count();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
            'insights' => [
                [
                    'label' => 'Programas analizados',
                    'value' => (string) $rows->count(),
                    'caption' => 'Programas que aportan al comparativo de demanda del periodo.',
                ],
                [
                    'label' => 'Ideas requeridas',
                    'value' => (string) $rows->sum('needed_ideas'),
                    'caption' => 'Demanda proyectada construida desde los grupos PG1 esperados.',
                ],
                [
                    'label' => 'Ideas disponibles',
                    'value' => (string) $rows->sum('available_ideas'),
                    'caption' => 'Ideas aprobadas y sin asignar que siguen activas en el banco.',
                ],
                [
                    'label' => 'Programas cubiertos',
                    'value' => (string) $coveragePrograms,
                    'caption' => 'Programas cuya demanda no presenta faltantes con el banco actual.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'ideas-needed-by-program',
                    'Demanda proyectada por programa',
                    'Cuenta cuantas ideas deberian existir para cubrir los grupos PG1 esperados.',
                    $this->reports->reportDataFromLabelCounts($neededIdeas->all()),
                    'Ideas requeridas',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'ideas-available-by-program',
                    'Banco disponible por programa',
                    'Mide las ideas aprobadas y sin asignar que realmente pueden cubrir la demanda.',
                    $this->reports->reportDataFromLabelCounts($availableIdeas->all()),
                    'Ideas disponibles',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'ideas-missing-by-program',
                    'Faltantes por programa',
                    'Muestra la brecha entre la demanda proyectada y la oferta actual del banco.',
                    $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
                    'Ideas faltantes',
                    'ideas'
                ),
            ],
            'table' => [
                'title' => 'Comparativo de demanda y oferta',
                'description' => 'Detalle por programa de la demanda esperada frente al banco disponible para el periodo.',
                'columns' => ['Programa', 'Ideas requeridas', 'Ideas disponibles', 'Faltantes', 'Excedente'],
                'rows' => $rows
                    ->sortByDesc('missing_ideas')
                    ->map(fn (array $row) => [
                        $row['program']?->name ?? 'Sin programa',
                        (string) $row['needed_ideas'],
                        (string) $row['available_ideas'],
                        (string) $row['missing_ideas'],
                        (string) $row['excess_ideas'],
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildIdeaCoverageStatusReportState(Collection $rows): array
    {
        $statuses = $rows
            ->map(fn (array $row) => $this->ideaCoverageLabel($row))
            ->countBy()
            ->sortDesc();
        $excessIdeas = $rows
            ->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['excess_ideas']])
            ->sortDesc();
        $missingIdeas = $rows
            ->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['missing_ideas']])
            ->sortDesc();
        $statusLeader = $statuses->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($statuses->all()),
            'insights' => [
                [
                    'label' => 'Estado mas frecuente',
                    'value' => (string) ($statusLeader ?? 'Sin datos'),
                    'caption' => 'Lectura dominante del equilibrio entre demanda y banco disponible.',
                ],
                [
                    'label' => 'Programas con faltantes',
                    'value' => (string) $rows->filter(fn (array $row) => (int) $row['missing_ideas'] > 0)->count(),
                    'caption' => 'Cantidad de programas que aun requieren nuevas ideas.',
                ],
                [
                    'label' => 'Programas con excedente',
                    'value' => (string) $rows->filter(fn (array $row) => (int) $row['excess_ideas'] > 0)->count(),
                    'caption' => 'Programas cuyo banco supera la demanda proyectada.',
                ],
                [
                    'label' => 'Brecha total',
                    'value' => (string) $rows->sum('missing_ideas'),
                    'caption' => 'Ideas adicionales necesarias para cubrir por completo el periodo filtrado.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'idea-coverage-status',
                    'Estado de cobertura por programa',
                    'Clasifica rapidamente el nivel de cumplimiento del banco frente a la demanda.',
                    $this->reports->reportDataFromLabelCounts($statuses->all()),
                    'Programas clasificados',
                    'programas'
                ),
                $this->reports->makeVisual(
                    'idea-excess-by-program',
                    'Excedente de ideas por programa',
                    'Resalta donde existen ideas disponibles por encima de la demanda proyectada.',
                    $this->reports->reportDataFromLabelCounts($excessIdeas->all()),
                    'Ideas en excedente',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'idea-gap-by-program',
                    'Brecha pendiente por programa',
                    'Destaca los programas que aun no alcanzan a cubrir la demanda del periodo.',
                    $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
                    'Ideas faltantes',
                    'ideas'
                ),
            ],
            'table' => [
                'title' => 'Estado de cobertura por programa',
                'description' => 'Clasificacion operativa del banco frente a la demanda del periodo seleccionado.',
                'columns' => ['Programa', 'Estado', 'Ideas requeridas', 'Disponibles', 'Faltantes'],
                'rows' => $rows
                    ->map(fn (array $row) => [
                        $row['program']?->name ?? 'Sin programa',
                        $this->ideaCoverageLabel($row),
                        (string) $row['needed_ideas'],
                        (string) $row['available_ideas'],
                        (string) $row['missing_ideas'],
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildIdeaThematicBalanceReportState(Collection $rows): array
    {
        $lineCounts = $rows
            ->flatMap(fn (array $row) => collect($row['line_breakdown']))
            ->groupBy('name')
            ->map(fn (Collection $items) => (int) $items->sum('count'))
            ->sortDesc();
        $areaCounts = $rows
            ->flatMap(fn (array $row) => collect($row['area_breakdown']))
            ->groupBy(fn (array $item) => $item['name'] . ' / ' . ($item['line_name'] ?: 'Sin linea'))
            ->map(fn (Collection $items) => (int) $items->sum('count'))
            ->sortDesc()
            ->take(12);
        $missingIdeas = $rows
            ->mapWithKeys(fn (array $row) => [$row['program']?->name ?? 'Sin programa' => (int) $row['missing_ideas']])
            ->sortDesc();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($lineCounts->all()),
            'insights' => [
                [
                    'label' => 'Lineas con ideas',
                    'value' => (string) $lineCounts->count(),
                    'caption' => 'Lineas de investigacion que hoy tienen al menos una idea disponible.',
                ],
                [
                    'label' => 'Areas destacadas',
                    'value' => (string) $areaCounts->count(),
                    'caption' => 'Areas tematicas mostradas en el analisis agregado del banco.',
                ],
                [
                    'label' => 'Ideas disponibles',
                    'value' => (string) $rows->sum('available_ideas'),
                    'caption' => 'Total de ideas aprobadas y sin asignar consideradas en el reporte.',
                ],
                [
                    'label' => 'Faltantes del periodo',
                    'value' => (string) $rows->sum('missing_ideas'),
                    'caption' => 'Brecha pendiente despues de distribuir el banco disponible.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'idea-lines-balance',
                    'Ideas disponibles por linea',
                    'Resume donde se concentra la oferta actual del banco institucional.',
                    $this->reports->reportDataFromLabelCounts($lineCounts->all()),
                    'Ideas por linea',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'idea-areas-balance',
                    'Ideas disponibles por area tematica',
                    'Permite detectar concentraciones o vacios dentro de las areas del banco.',
                    $this->reports->reportDataFromLabelCounts($areaCounts->all()),
                    'Ideas por area',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'idea-missing-context',
                    'Faltantes por programa',
                    'Relaciona la brecha de ideas con la composicion tematica actual del banco.',
                    $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
                    'Ideas faltantes',
                    'ideas'
                ),
            ],
            'table' => [
                'title' => 'Detalle tematico del banco',
                'description' => 'Agrega la disponibilidad por linea y area tomando el conjunto filtrado del periodo.',
                'columns' => ['Tipo', 'Nombre', 'Ideas disponibles'],
                'rows' => $lineCounts
                    ->map(fn (int $count, string $label) => ['Linea', $label, (string) $count])
                    ->concat($areaCounts->map(fn (int $count, string $label) => ['Area', $label, (string) $count]))
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function ideaCoverageLabel(array $row): string
    {
        if ((int) $row['needed_ideas'] === 0) {
            return 'Sin demanda proyectada';
        }

        if ((int) $row['available_ideas'] === 0) {
            return 'Sin ideas disponibles';
        }

        if ((int) $row['missing_ideas'] > 0) {
            return 'Cobertura parcial';
        }

        if ((int) $row['excess_ideas'] > 0) {
            return 'Con excedente';
        }

        return 'Cobertura exacta';
    }

    /**
     * @return array<int, string>
     */
    protected function ideaDemandReportFiltersSummary(
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId
    ): array {
        $summary = [];

        $period = $periods->firstWhere('id', $selectedPeriodId);
        $programIds = ResearchStaffProgram::equivalentIds($selectedProgramId);
        $program = $programs->first(fn ($item) => in_array((int) $item->id, $programIds, true));

        $summary[] = $period
            ? 'Periodo academico: ' . $period->name
            : 'Periodo academico: sin seleccion especifica.';

        $summary[] = $program
            ? 'Programa: ' . $program->name
            : 'Programa: todos los programas del periodo.';

        return $summary;
    }
}
