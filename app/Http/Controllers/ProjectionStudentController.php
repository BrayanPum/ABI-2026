<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Services\Projections\ProjectionPeriodService;
use App\Services\Projections\StudentProjectionService;
use App\Services\Reports\VisualReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectionStudentController extends Controller
{
    public function __construct(
        private readonly ProjectionPeriodService $periods,
        private readonly StudentProjectionService $students,
        private readonly VisualReportService $reports,
    )
    {
    }

    public function index(Request $request): View|RedirectResponse|Response
    {
        $periods = $this->periods->allPeriods();
        $activePeriod = $this->periods->activePeriod();
        $selectedPeriodId = $request->integer('academic_period_id') ?: $activePeriod?->id ?: $periods->first()?->id;
        $selectedProgramId = $request->integer('program_id');
        $selectedStage = (string) $request->input('stage', '');
        $selectedState = $request->input('state');
        $selectedState = in_array((string) $selectedState, ['0', '1'], true)
            ? (string) $selectedState
            : '';
        $perPage = max(10, min((int) $request->input('per_page', 10), 100));
        $page = max((int) $request->input('page', 1), 1);

        $rows = $this->students->supportRows($selectedProgramId ?: null, $selectedPeriodId)
            ->when($selectedStage !== '', fn ($collection) => $collection->where('pg_stage_key', $selectedStage))
            ->when($selectedState !== '', function ($collection) use ($selectedState) {
                return $collection->filter(fn (array $row) => (int) $row['is_active'] === (int) $selectedState)->values();
            })
            ->values();

        $summary = [
            'total_students' => $rows->count(),
            'active_students' => $rows->where('is_active', true)->count(),
            'pg1_students' => $rows->where('pg_stage_key', 'pg1')->count(),
            'pg2_students' => $rows->where('pg_stage_key', 'pg2')->count(),
            'projected_pg2_students' => $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
        ];

        $programs = ResearchStaffProgram::query()
            ->orderBy('name')
            ->orderBy('code')
            ->get();
        $reportState = $this->buildStudentProjectionReportState(
            $request,
            $rows,
            $periods,
            $programs,
            $selectedPeriodId,
            $selectedProgramId,
            $selectedStage,
            $selectedState
        );

        if ($reportState['export'] === 'pdf') {
            return $this->reports->downloadPdf(
                (string) $reportState['reportKey'],
                (string) $reportState['reportLabel'],
                $this->studentProjectionReportModules()[$reportState['reportKey']]['description'] ?? 'Resumen de base estudiantil.',
                $reportState['insights'],
                $reportState['visuals'],
                $reportState['table'],
                $this->studentProjectionReportFiltersSummary($periods, $programs, $selectedPeriodId, $selectedProgramId, $selectedStage, $selectedState),
                'Reporte de base estudiantil',
                'base-estudiantil'
            );
        }

        $lastPage = max((int) ceil(max($rows->count(), 1) / $perPage), 1);

        if ($request->has('page') && $page > $lastPage) {
            return redirect()->route('projections.students.index', array_merge(
                $request->except('page'),
                ['page' => 1]
            ));
        }

        $paginatedRows = new LengthAwarePaginator(
            $rows->slice(($page - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => route('projections.students.index'),
                'query' => $request->query(),
            ]
        );

        return view('projections.students.index', [
            'rows' => $paginatedRows,
            'periods' => $periods,
            'programs' => $programs,
            'stageOptions' => $this->students->stageOptions(),
            'selectedPeriodId' => $selectedPeriodId,
            'selectedProgramId' => $selectedProgramId,
            'selectedStage' => $selectedStage,
            'selectedState' => $selectedState,
            'perPage' => $perPage,
            'summary' => $summary,
            'reportModules' => $this->studentProjectionReportModules(),
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
    protected function studentProjectionReportModules(): array
    {
        return [
            'student_stage_distribution' => [
                'label' => 'Distribucion por etapa',
                'description' => 'Resume como se compone la base estudiantil entre PG1, PG2 y continuidad proyectada.',
            ],
            'student_program_distribution' => [
                'label' => 'Distribucion por programa',
                'description' => 'Compara el tamano de la base estudiantil y la continuidad proyectada entre programas.',
            ],
            'student_activity_distribution' => [
                'label' => 'Actividad y proyecto',
                'description' => 'Relaciona estado del estudiante, asignacion actual y continuidad proyectada para el periodo filtrado.',
            ],
        ];
    }

    protected function buildStudentProjectionReportState(
        Request $request,
        Collection $rows,
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId,
        string $selectedStage,
        string $selectedState
    ): array {
        $filters = $request->validate([
            'report_key' => ['nullable', Rule::in(array_keys($this->studentProjectionReportModules()))],
            'report_export' => ['nullable', Rule::in(['pdf'])],
        ]);

        $reportKey = $filters['report_key'] ?? 'student_stage_distribution';
        $reportState = $this->composeStudentProjectionReportState($reportKey, $rows);

        return [
            'filters' => [
                'report_key' => $reportKey,
                'academic_period_id' => $selectedPeriodId,
                'program_id' => $selectedProgramId,
                'stage' => $selectedStage,
                'state' => $selectedState,
            ],
            'reportKey' => $reportKey,
            'reportLabel' => $this->studentProjectionReportModules()[$reportKey]['label'] ?? 'Reporte de base estudiantil',
            'reportData' => $reportState['reportData'],
            'insights' => $reportState['insights'],
            'visuals' => $reportState['visuals'],
            'table' => $reportState['table'],
            'export' => $filters['report_export'] ?? null,
        ];
    }

    protected function composeStudentProjectionReportState(string $reportKey, Collection $rows): array
    {
        return match ($reportKey) {
            'student_program_distribution' => $this->buildStudentProgramDistributionReportState($rows),
            'student_activity_distribution' => $this->buildStudentActivityDistributionReportState($rows),
            default => $this->buildStudentStageDistributionReportState($rows),
        };
    }

    protected function buildStudentStageDistributionReportState(Collection $rows): array
    {
        $stages = $rows->map(fn (array $row) => $row['pg_stage_label'])->countBy()->sortDesc();
        $activity = collect([
            'Activos' => $rows->where('is_active', true)->count(),
            'Inactivos' => $rows->where('is_active', false)->count(),
        ]);
        $projection = collect([
            'Se proyectan a PG2' => $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
            'Sin continuidad inmediata' => $rows->count() - $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
        ]);
        $mainStage = $stages->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($stages->all()),
            'insights' => [
                [
                    'label' => 'Estudiantes filtrados',
                    'value' => (string) $rows->count(),
                    'caption' => 'Base estudiantil considerada con los filtros activos.',
                ],
                [
                    'label' => 'Activos',
                    'value' => (string) $rows->where('is_active', true)->count(),
                    'caption' => 'Estudiantes con usuario activo dentro de la base filtrada.',
                ],
                [
                    'label' => 'Continuidad a PG2',
                    'value' => (string) $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
                    'caption' => 'Casos que pasan a PG2 en el siguiente corte segun el periodo seleccionado.',
                ],
                [
                    'label' => 'Etapa predominante',
                    'value' => (string) ($mainStage ?? 'Sin datos'),
                    'caption' => 'Etapa academica con mayor presencia en la base estudiantil filtrada.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'students-by-stage',
                    'Base estudiantil por etapa PG',
                    'Identifica el peso relativo de PG1 y PG2 dentro del periodo analizado.',
                    $this->reports->reportDataFromLabelCounts($stages->all()),
                    'Estudiantes por etapa',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-by-activity',
                    'Estado de actividad de la base',
                    'Separa la poblacion activa de la inactiva para depurar la lectura de continuidad.',
                    $this->reports->reportDataFromLabelCounts($activity->all()),
                    'Estado de estudiantes',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-by-projection',
                    'Continuidad proyectada hacia PG2',
                    'Permite ver cuantos estudiantes activos avanzan a PG2 en el siguiente periodo.',
                    $this->reports->reportDataFromLabelCounts($projection->all()),
                    'Casos de continuidad',
                    'estudiantes'
                ),
            ],
            'table' => [
                'title' => 'Resumen por programa',
                'description' => 'Agrupa la base estudiantil para facilitar el analisis de continuidad y cobertura.',
                'columns' => ['Programa', 'Total', 'Activos', 'PG1', 'PG2', 'Se proyectan a PG2'],
                'rows' => $this->studentProgramSummaryRows($rows),
            ],
        ];
    }

    protected function buildStudentProgramDistributionReportState(Collection $rows): array
    {
        $totals = $rows->groupBy(fn (array $row) => $row['program_name'] ?: 'Sin programa')
            ->map(fn (Collection $items) => $items->count())
            ->sortDesc();
        $projectedPg2 = $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])
            ->groupBy(fn (array $row) => $row['program_name'] ?: 'Sin programa')
            ->map(fn (Collection $items) => $items->count())
            ->sortDesc();
        $inactive = $rows->where('is_active', false)
            ->groupBy(fn (array $row) => $row['program_name'] ?: 'Sin programa')
            ->map(fn (Collection $items) => $items->count())
            ->sortDesc();
        $topProgram = $totals->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($totals->all()),
            'insights' => [
                [
                    'label' => 'Programas en la base',
                    'value' => (string) $totals->count(),
                    'caption' => 'Programas con al menos un estudiante dentro de los filtros seleccionados.',
                ],
                [
                    'label' => 'Programa principal',
                    'value' => (string) ($topProgram ?? 'Sin datos'),
                    'caption' => 'Programa con mayor cantidad de estudiantes en la base filtrada.',
                ],
                [
                    'label' => 'Continuidad proyectada',
                    'value' => (string) $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
                    'caption' => 'Estudiantes que alimentan la proyeccion automatica hacia PG2.',
                ],
                [
                    'label' => 'Inactivos filtrados',
                    'value' => (string) $rows->where('is_active', false)->count(),
                    'caption' => 'Casos inactivos que conviene revisar por posible ruido en la base.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'students-total-by-program',
                    'Base estudiantil por programa',
                    'Compara el tamano total de la base estudiantil entre programas.',
                    $this->reports->reportDataFromLabelCounts($totals->all()),
                    'Estudiantes por programa',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-projected-pg2-by-program',
                    'Continuidad proyectada por programa',
                    'Enfoca la cantidad de estudiantes que pasarian a PG2 desde cada programa.',
                    $this->reports->reportDataFromLabelCounts($projectedPg2->all()),
                    'Continuidad a PG2',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-inactive-by-program',
                    'Estudiantes inactivos por programa',
                    'Ayuda a detectar programas donde la base requiere saneamiento o seguimiento.',
                    $this->reports->reportDataFromLabelCounts($inactive->all()),
                    'Estudiantes inactivos',
                    'estudiantes'
                ),
            ],
            'table' => [
                'title' => 'Detalle agregado por programa',
                'description' => 'Lectura consolidada de la base, actividad y continuidad esperada por programa.',
                'columns' => ['Programa', 'Total', 'Activos', 'PG1', 'PG2', 'Se proyectan a PG2'],
                'rows' => $this->studentProgramSummaryRows($rows),
            ],
        ];
    }

    protected function buildStudentActivityDistributionReportState(Collection $rows): array
    {
        $projectState = collect([
            'Con proyecto' => $rows->filter(fn (array $row) => ! empty($row['project_title']))->count(),
            'Sin proyecto' => $rows->filter(fn (array $row) => empty($row['project_title']))->count(),
        ]);
        $activity = collect([
            'Activos' => $rows->where('is_active', true)->count(),
            'Inactivos' => $rows->where('is_active', false)->count(),
        ]);
        $projection = collect([
            'Continuidad a PG2' => $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
            'Sin continuidad' => $rows->count() - $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
        ]);

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($projectState->all()),
            'insights' => [
                [
                    'label' => 'Con proyecto asociado',
                    'value' => (string) $projectState->get('Con proyecto', 0),
                    'caption' => 'Estudiantes con idea o proyecto visible al momento del corte.',
                ],
                [
                    'label' => 'Sin proyecto asociado',
                    'value' => (string) $projectState->get('Sin proyecto', 0),
                    'caption' => 'Casos que aun no tienen proyecto y pueden requerir seguimiento.',
                ],
                [
                    'label' => 'Activos sin proyecto',
                    'value' => (string) $rows->filter(fn (array $row) => $row['is_active'] && empty($row['project_title']))->count(),
                    'caption' => 'Subconjunto operativo que puede convertirse en demanda futura del banco.',
                ],
                [
                    'label' => 'Continuidad activa',
                    'value' => (string) $rows->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
                    'caption' => 'Estudiantes activos que pasan a la siguiente etapa de proyecto.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'students-project-state',
                    'Estudiantes con y sin proyecto',
                    'Separa la base segun exista o no un proyecto asociado dentro del periodo analizado.',
                    $this->reports->reportDataFromLabelCounts($projectState->all()),
                    'Estado de proyecto',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-activity-state',
                    'Estado de usuario en la base',
                    'Aclara el peso de estudiantes activos e inactivos dentro del corte consultado.',
                    $this->reports->reportDataFromLabelCounts($activity->all()),
                    'Estado de actividad',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'students-pg2-continuity',
                    'Continuidad efectiva a PG2',
                    'Contrasta quienes pasan a PG2 frente a quienes no lo hacen con el filtro actual.',
                    $this->reports->reportDataFromLabelCounts($projection->all()),
                    'Continuidad efectiva',
                    'estudiantes'
                ),
            ],
            'table' => [
                'title' => 'Casos operativos de la base',
                'description' => 'Relaciona estado, etapa y proyecto para priorizar seguimiento academico.',
                'columns' => ['Estudiante', 'Programa', 'Estado', 'Etapa PG', 'Proyecto', 'Periodo de referencia'],
                'rows' => $rows
                    ->map(fn (array $row) => [
                        $row['full_name'],
                        $row['program_name'] ?: 'Sin programa',
                        $row['is_active'] ? 'Activo' : 'Inactivo',
                        $row['pg_stage_label'],
                        $row['project_title'] ?: 'Sin proyecto',
                        $row['reference_period_name'] ?: 'Sin periodo',
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function studentProgramSummaryRows(Collection $rows): array
    {
        return $rows
            ->groupBy(fn (array $row) => $row['program_name'] ?: 'Sin programa')
            ->map(function (Collection $items, string $programName): array {
                return [
                    $programName,
                    (string) $items->count(),
                    (string) $items->where('is_active', true)->count(),
                    (string) $items->where('pg_stage_key', 'pg1')->count(),
                    (string) $items->where('pg_stage_key', 'pg2')->count(),
                    (string) $items->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])->count(),
                ];
            })
            ->sortByDesc(fn (array $row) => (int) $row[1])
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function studentProjectionReportFiltersSummary(
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId,
        string $selectedStage,
        string $selectedState
    ): array {
        $summary = [];

        $period = $periods->firstWhere('id', $selectedPeriodId);
        $program = $programs->firstWhere('id', $selectedProgramId);

        $summary[] = $period
            ? 'Periodo academico: ' . $period->name
            : 'Periodo academico: sin seleccion especifica.';

        $summary[] = $program
            ? 'Programa: ' . $program->name
            : 'Programa: todos los programas.';

        if ($selectedStage !== '') {
            $summary[] = 'Etapa PG: ' . ($this->students->stageOptions()[$selectedStage] ?? $selectedStage);
        }

        if ($selectedState !== '') {
            $summary[] = 'Estado usuario: ' . ($selectedState === '1' ? 'Activos' : 'Inactivos');
        }

        return $summary;
    }
}
