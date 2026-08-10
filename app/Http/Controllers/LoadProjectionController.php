<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoadProjectionRequest;
use App\Models\AcademicProcessWindow;
use App\Models\ResearchStaff\ResearchStaffLoadProjection;
use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Services\AcademicCalendar\AcademicCalendarService;
use App\Services\Projections\LoadProjectionService;
use App\Services\Projections\ProjectionPeriodService;
use App\Services\Reports\VisualReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LoadProjectionController extends Controller
{
    public function __construct(
        private readonly ProjectionPeriodService $periods,
        private readonly LoadProjectionService $loadProjections,
        private readonly VisualReportService $reports,
    ) {
    }

    public function index(Request $request): View|RedirectResponse|Response
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION);
        }

        $periods = $this->periods->allPeriods();
        $targetPeriod = $this->periods->targetPeriod();
        $selectedPeriodId = $request->integer('academic_period_id') ?: $targetPeriod?->id ?: $periods->first()?->id;
        $selectedProgramId = $request->integer('program_id');
        $selectedProgramIds = ResearchStaffProgram::equivalentIds($selectedProgramId);
        $perPage = max(10, min((int) $request->input('per_page', 10), 100));
        $programs = ResearchStaffProgram::uniqueOptions();

        $baseQuery = ResearchStaffLoadProjection::query()
            ->with(['academicPeriod', 'program.researchGroup'])
            ->when($selectedPeriodId, fn ($query) => $query->where('academic_period_id', $selectedPeriodId))
            ->when($selectedProgramIds !== [], fn ($query) => $query->whereIn('program_id', $selectedProgramIds))
            ->orderByDesc('academic_period_id')
            ->orderBy('program_id');

        $reportRows = (clone $baseQuery)->get();
        $reportState = $this->buildLoadProjectionReportState(
            $request,
            $reportRows,
            $periods,
            $programs,
            $selectedPeriodId,
            $selectedProgramId
        );

        if ($reportState['export'] === 'pdf') {
            return $this->reports->downloadPdf(
                (string) $reportState['reportKey'],
                (string) $reportState['reportLabel'],
                $this->loadProjectionReportModules()[$reportState['reportKey']]['description'] ?? 'Resumen de proyeccion de carga.',
                $reportState['insights'],
                $reportState['visuals'],
                $reportState['table'],
                $this->loadProjectionReportFiltersSummary($periods, $programs, $selectedPeriodId, $selectedProgramId),
                'Reporte de proyeccion de carga',
                'proyeccion-carga'
            );
        }

        $projections = $baseQuery
            ->paginate($perPage)
            ->appends($request->query());

        return view('projections.load-projections.index', [
            'projections' => $projections,
            'periods' => $periods,
            'programs' => $programs,
            'targetPeriod' => $targetPeriod,
            'selectedPeriodId' => $selectedPeriodId,
            'selectedProgramId' => $selectedProgramId,
            'perPage' => $perPage,
            'reportModules' => $this->loadProjectionReportModules(),
            'reportFilters' => $reportState['filters'],
            'reportData' => $reportState['reportData'],
            'reportInsights' => $reportState['insights'],
            'reportVisuals' => $reportState['visuals'],
            'reportTable' => $reportState['table'],
            'activeReportKey' => $reportState['reportKey'],
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION);
        }

        $targetPeriod = $this->periods->targetPeriod();
        $selectedProgramId = old('program_id') ? (int) old('program_id') : null;

        return view('projections.load-projections.create', [
            'loadProjection' => new ResearchStaffLoadProjection(),
            'targetPeriod' => $targetPeriod,
            'activePeriod' => $this->periods->activePeriod(),
            'programs' => ResearchStaffProgram::uniqueOptions(),
            'previewMetrics' => $this->loadProjections->preview($selectedProgramId, old('projected_pg1_students')),
            'lockProgram' => false,
            'isCurrentTarget' => true,
        ]);
    }

    public function store(LoadProjectionRequest $request): RedirectResponse|View
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION);
        }

        [$projection, $created] = $this->loadProjections->upsert($request->validated());

        return redirect()
            ->route('projections.load-projections.index', [
                'academic_period_id' => $projection->academic_period_id,
                'program_id' => $projection->program_id,
            ])
            ->with(
                'success',
                $created
                    ? 'Proyeccion de carga registrada correctamente.'
                    : 'Ya existia una proyeccion para este programa y periodo. La informacion fue actualizada.'
            );
    }

    public function edit(ResearchStaffLoadProjection $load_projection): View|RedirectResponse
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION);
        }

        $load_projection->load(['academicPeriod', 'program.researchGroup']);

        return view('projections.load-projections.edit', [
            'loadProjection' => $load_projection,
            'targetPeriod' => $load_projection->academicPeriod,
            'activePeriod' => $this->periods->activePeriod(),
            'programs' => ResearchStaffProgram::uniqueOptions(),
            'previewMetrics' => $this->loadProjections->preview(
                (int) $load_projection->program_id,
                old('projected_pg1_students', $load_projection->projected_pg1_students),
                $load_projection
            ),
            'lockProgram' => true,
            'isCurrentTarget' => $this->periods->isCurrentTarget($load_projection->academic_period_id),
        ]);
    }

    public function update(LoadProjectionRequest $request, ResearchStaffLoadProjection $load_projection): RedirectResponse|View
    {
        if (! AcademicCalendarService::isProcessWindowOpen(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION)) {
            return $this->academicProcessUnavailableView(AcademicProcessWindow::PROCESS_TEACHER_LOAD_PROJECTION);
        }

        $this->loadProjections->update($load_projection, $request->validated());

        return redirect()
            ->route('projections.load-projections.index', [
                'academic_period_id' => $load_projection->academic_period_id,
                'program_id' => $load_projection->program_id,
            ])
            ->with('success', 'Proyeccion de carga actualizada correctamente.');
    }

    /**
     * @return array<string, array{label: string, description: string}>
     */
    protected function loadProjectionReportModules(): array
    {
        return [
            'hours_by_program' => [
                'label' => 'Horas por programa',
                'description' => 'Compara la carga semanal proyectada entre programas para el periodo seleccionado.',
            ],
            'student_projection_by_program' => [
                'label' => 'Estudiantes proyectados',
                'description' => 'Distribuye la proyeccion de estudiantes PG1 y PG2 por programa.',
            ],
            'stage_load_mix' => [
                'label' => 'Balance PG1 vs PG2',
                'description' => 'Resume el peso total de PG1 y PG2 en estudiantes, grupos y horas.',
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
    protected function buildLoadProjectionReportState(
        Request $request,
        Collection $projections,
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId
    ): array {
        $filters = $request->validate([
            'report_key' => ['nullable', Rule::in(array_keys($this->loadProjectionReportModules()))],
            'report_export' => ['nullable', Rule::in(['pdf'])],
        ]);

        $reportKey = $filters['report_key'] ?? 'hours_by_program';
        $reportState = $this->composeLoadProjectionReportState($reportKey, $projections);

        return [
            'filters' => [
                'report_key' => $reportKey,
                'academic_period_id' => $selectedPeriodId,
                'program_id' => $selectedProgramId,
            ],
            'reportKey' => $reportKey,
            'reportLabel' => $this->loadProjectionReportModules()[$reportKey]['label'] ?? 'Reporte de carga',
            'reportData' => $reportState['reportData'],
            'insights' => $reportState['insights'],
            'visuals' => $reportState['visuals'],
            'table' => $reportState['table'],
            'export' => $filters['report_export'] ?? null,
        ];
    }

    /**
     * @return array{
     *     reportData: array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int},
     *     insights: array<int, array{label: string, value: string, caption: string}>,
     *     visuals: array<int, array<string, mixed>>,
     *     table: ?array{title: string, description: string, columns: array<int, string>, rows: array<int, array<int, string>>}
     * }
     */
    protected function composeLoadProjectionReportState(string $reportKey, Collection $projections): array
    {
        return match ($reportKey) {
            'student_projection_by_program' => $this->buildLoadStudentProjectionReportState($projections),
            'stage_load_mix' => $this->buildLoadStageMixReportState($projections),
            default => $this->buildLoadHoursReportState($projections),
        };
    }

    /**
     * @return array{
     *     reportData: array{categories: array<int, string>, values: array<int, int>, percentages: array<int, float>, total: int},
     *     insights: array<int, array{label: string, value: string, caption: string}>,
     *     visuals: array<int, array<string, mixed>>,
     *     table: array{title: string, description: string, columns: array<int, string>, rows: array<int, array<int, string>>}
     * }
     */
    protected function buildLoadHoursReportState(Collection $projections): array
    {
        $totalHours = $this->projectionCountsByProgram($projections, 'total_weekly_hours');
        $pg1Hours = $this->projectionCountsByProgram($projections, 'pg1_weekly_hours');
        $pg2Hours = $this->projectionCountsByProgram($projections, 'pg2_weekly_hours');
        $totalGroups = $projections->sum(fn (ResearchStaffLoadProjection $projection) => (int) $projection->projected_pg1_groups + (int) $projection->projected_pg2_groups);
        $topProgram = $totalHours->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($totalHours->all()),
            'insights' => [
                [
                    'label' => 'Programas proyectados',
                    'value' => (string) $projections->count(),
                    'caption' => 'Programas con registro de carga para el periodo filtrado.',
                ],
                [
                    'label' => 'Horas semanales',
                    'value' => (string) $projections->sum('total_weekly_hours'),
                    'caption' => 'Suma total de horas proyectadas entre PG1 y PG2.',
                ],
                [
                    'label' => 'Grupos proyectados',
                    'value' => (string) $totalGroups,
                    'caption' => 'Total de grupos estimados con la regla actual de 3 estudiantes.',
                ],
                [
                    'label' => 'Programa con mayor carga',
                    'value' => (string) ($topProgram ?? 'Sin datos'),
                    'caption' => 'Programa que concentra mas horas semanales en la proyeccion.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'load-total-hours',
                    'Horas semanales totales por programa',
                    'Mide la carga docente total requerida por cada programa.',
                    $this->reports->reportDataFromLabelCounts($totalHours->all()),
                    'Horas proyectadas',
                    'horas'
                ),
                $this->reports->makeVisual(
                    'load-pg1-hours',
                    'Horas PG1 por programa',
                    'Aisla el esfuerzo semanal asociado a la direccion de grupos PG1.',
                    $this->reports->reportDataFromLabelCounts($pg1Hours->all()),
                    'Horas PG1',
                    'horas'
                ),
                $this->reports->makeVisual(
                    'load-pg2-hours',
                    'Horas PG2 por programa',
                    'Permite comparar el peso semanal proyectado de continuidad en PG2.',
                    $this->reports->reportDataFromLabelCounts($pg2Hours->all()),
                    'Horas PG2',
                    'horas'
                ),
            ],
            'table' => [
                'title' => 'Detalle de carga por programa',
                'description' => 'Resume estudiantes, grupos y horas calculadas para cada programa del periodo.',
                'columns' => ['Programa', 'PG1 estudiantes', 'PG2 estudiantes', 'PG1 grupos', 'PG2 grupos', 'Horas totales'],
                'rows' => $projections
                    ->sortByDesc('total_weekly_hours')
                    ->map(fn (ResearchStaffLoadProjection $projection) => [
                        $projection->program?->name ?? 'Sin programa',
                        (string) $projection->projected_pg1_students,
                        (string) $projection->projected_pg2_students,
                        (string) $projection->projected_pg1_groups,
                        (string) $projection->projected_pg2_groups,
                        (string) $projection->total_weekly_hours,
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildLoadStudentProjectionReportState(Collection $projections): array
    {
        $totalStudents = $projections
            ->groupBy(fn (ResearchStaffLoadProjection $projection) => $projection->program?->name ?? 'Sin programa')
            ->map(fn (Collection $rows) => $rows->sum('projected_pg1_students') + $rows->sum('projected_pg2_students'))
            ->sortDesc();
        $pg1Students = $this->projectionCountsByProgram($projections, 'projected_pg1_students');
        $pg2Students = $this->projectionCountsByProgram($projections, 'projected_pg2_students');
        $topProgram = $totalStudents->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($totalStudents->all()),
            'insights' => [
                [
                    'label' => 'Estudiantes proyectados',
                    'value' => (string) ($projections->sum('projected_pg1_students') + $projections->sum('projected_pg2_students')),
                    'caption' => 'Total de estudiantes considerados en la proyeccion de carga.',
                ],
                [
                    'label' => 'PG1 proyectado',
                    'value' => (string) $projections->sum('projected_pg1_students'),
                    'caption' => 'Estudiantes PG1 esperados para el periodo objetivo.',
                ],
                [
                    'label' => 'PG2 proyectado',
                    'value' => (string) $projections->sum('projected_pg2_students'),
                    'caption' => 'Continuidad automatica estimada hacia PG2.',
                ],
                [
                    'label' => 'Programa con mayor base',
                    'value' => (string) ($topProgram ?? 'Sin datos'),
                    'caption' => 'Programa con mayor volumen total de estudiantes proyectados.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'load-total-students',
                    'Estudiantes proyectados por programa',
                    'Suma la proyeccion conjunta de estudiantes PG1 y PG2.',
                    $this->reports->reportDataFromLabelCounts($totalStudents->all()),
                    'Estudiantes proyectados',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'load-pg1-students',
                    'PG1 proyectado por programa',
                    'Permite contrastar la demanda inicial de direccion de proyectos entre programas.',
                    $this->reports->reportDataFromLabelCounts($pg1Students->all()),
                    'Estudiantes PG1',
                    'estudiantes'
                ),
                $this->reports->makeVisual(
                    'load-pg2-students',
                    'PG2 proyectado por programa',
                    'Muestra la continuidad estimada hacia la fase PG2 para cada programa.',
                    $this->reports->reportDataFromLabelCounts($pg2Students->all()),
                    'Estudiantes PG2',
                    'estudiantes'
                ),
            ],
            'table' => [
                'title' => 'Base de estudiantes proyectados',
                'description' => 'Consolida los estudiantes estimados por programa y etapa de proyecto.',
                'columns' => ['Programa', 'PG1', 'PG2', 'Total', 'Grupos totales', 'Horas totales'],
                'rows' => $projections
                    ->sortByDesc(fn (ResearchStaffLoadProjection $projection) => (int) $projection->projected_pg1_students + (int) $projection->projected_pg2_students)
                    ->map(fn (ResearchStaffLoadProjection $projection) => [
                        $projection->program?->name ?? 'Sin programa',
                        (string) $projection->projected_pg1_students,
                        (string) $projection->projected_pg2_students,
                        (string) ((int) $projection->projected_pg1_students + (int) $projection->projected_pg2_students),
                        (string) ((int) $projection->projected_pg1_groups + (int) $projection->projected_pg2_groups),
                        (string) $projection->total_weekly_hours,
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildLoadStageMixReportState(Collection $projections): array
    {
        $stageStudents = collect([
            'PG1' => (int) $projections->sum('projected_pg1_students'),
            'PG2' => (int) $projections->sum('projected_pg2_students'),
        ]);
        $stageGroups = collect([
            'PG1' => (int) $projections->sum('projected_pg1_groups'),
            'PG2' => (int) $projections->sum('projected_pg2_groups'),
        ]);
        $stageHours = collect([
            'PG1' => (int) $projections->sum('pg1_weekly_hours'),
            'PG2' => (int) $projections->sum('pg2_weekly_hours'),
        ]);
        $dominantStage = $stageHours->sortDesc()->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($stageHours->all()),
            'insights' => [
                [
                    'label' => 'Etapa dominante',
                    'value' => (string) ($dominantStage ?? 'Sin datos'),
                    'caption' => 'Etapa que concentra la mayor cantidad de horas en el periodo.',
                ],
                [
                    'label' => 'Horas PG1',
                    'value' => (string) $stageHours->get('PG1', 0),
                    'caption' => 'Carga semanal consolidada para PG1.',
                ],
                [
                    'label' => 'Horas PG2',
                    'value' => (string) $stageHours->get('PG2', 0),
                    'caption' => 'Carga semanal consolidada para PG2.',
                ],
                [
                    'label' => 'Estudiantes totales',
                    'value' => (string) $stageStudents->sum(),
                    'caption' => 'Total de estudiantes considerados al comparar ambas etapas.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'load-stage-hours',
                    'Horas por etapa',
                    'Compara el esfuerzo semanal requerido entre PG1 y PG2.',
                    $this->reports->reportDataFromLabelCounts($stageHours->all()),
                    'Horas por etapa',
                    'horas'
                ),
                $this->reports->makeVisual(
                    'load-stage-groups',
                    'Grupos por etapa',
                    'Muestra la cantidad de grupos que sustentan la proyeccion de carga.',
                    $this->reports->reportDataFromLabelCounts($stageGroups->all()),
                    'Grupos proyectados',
                    'grupos'
                ),
                $this->reports->makeVisual(
                    'load-stage-students',
                    'Estudiantes por etapa',
                    'Relaciona el volumen de estudiantes PG1 y PG2 del periodo objetivo.',
                    $this->reports->reportDataFromLabelCounts($stageStudents->all()),
                    'Estudiantes por etapa',
                    'estudiantes'
                ),
            ],
            'table' => [
                'title' => 'Balance consolidado por etapa',
                'description' => 'Sirve como lectura rapida del peso relativo entre PG1 y PG2.',
                'columns' => ['Etapa', 'Estudiantes', 'Grupos', 'Horas'],
                'rows' => [
                    ['PG1', (string) $stageStudents->get('PG1', 0), (string) $stageGroups->get('PG1', 0), (string) $stageHours->get('PG1', 0)],
                    ['PG2', (string) $stageStudents->get('PG2', 0), (string) $stageGroups->get('PG2', 0), (string) $stageHours->get('PG2', 0)],
                ],
            ],
        ];
    }

    protected function projectionCountsByProgram(Collection $projections, string $field): Collection
    {
        return $projections
            ->groupBy(fn (ResearchStaffLoadProjection $projection) => $projection->program?->name ?? 'Sin programa')
            ->map(fn (Collection $rows) => (int) $rows->sum($field))
            ->sortDesc();
    }

    /**
     * @return array<int, string>
     */
    protected function loadProjectionReportFiltersSummary(
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
