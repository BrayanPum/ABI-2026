<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Models\ResearchStaff\ResearchStaffTeacherAssignment;
use App\Services\Projections\ProjectionPeriodService;
use App\Services\Projections\TeacherProjectionService;
use App\Services\Reports\VisualReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectionProfessorController extends Controller
{
    public function __construct(
        private readonly ProjectionPeriodService $periods,
        private readonly TeacherProjectionService $teachers,
        private readonly VisualReportService $reports,
    ) {
    }

    public function index(Request $request): View|Response
    {
        $periods = $this->periods->allPeriods();
        $targetPeriod = $this->periods->targetPeriod();
        $selectedPeriodId = $request->integer('academic_period_id') ?: $targetPeriod?->id ?: $periods->first()?->id;
        $selectedProgramId = $request->integer('program_id');
        $selectedProgramIds = ResearchStaffProgram::equivalentIds($selectedProgramId);
        $perPage = max(10, min((int) $request->input('per_page', 10), 100));
        $programs = ResearchStaffProgram::uniqueOptions();

        $baseQuery = ResearchStaffTeacherAssignment::query()
            ->with(['academicPeriod', 'program', 'professor.user', 'professor.cityProgram.city'])
            ->when($selectedPeriodId, fn ($query) => $query->where('academic_period_id', $selectedPeriodId))
            ->when($selectedProgramIds !== [], fn ($query) => $query->whereIn('program_id', $selectedProgramIds))
            ->orderBy('program_id')
            ->orderBy('professor_id');

        $summaryAssignments = $this->teachers->decorateAssignments((clone $baseQuery)->get(), $selectedPeriodId);
        $reportState = $this->buildProfessorProjectionReportState(
            $request,
            $summaryAssignments,
            $periods,
            $programs,
            $selectedPeriodId,
            $selectedProgramId
        );

        if ($reportState['export'] === 'pdf') {
            return $this->reports->downloadPdf(
                (string) $reportState['reportKey'],
                (string) $reportState['reportLabel'],
                $this->professorProjectionReportModules()[$reportState['reportKey']]['description'] ?? 'Resumen de proyeccion docente.',
                $reportState['insights'],
                $reportState['visuals'],
                $reportState['table'],
                $this->professorProjectionReportFiltersSummary($periods, $programs, $selectedPeriodId, $selectedProgramId),
                'Reporte de proyeccion docente',
                'proyeccion-docentes'
            );
        }

        $assignments = $baseQuery->paginate($perPage)->appends($request->query());
        $assignments->setCollection($this->teachers->decorateAssignments($assignments->getCollection(), $selectedPeriodId));

        return view('projections.professors.index', [
            'assignments' => $assignments,
            'periods' => $periods,
            'programs' => $programs,
            'targetPeriod' => $targetPeriod,
            'selectedPeriodId' => $selectedPeriodId,
            'selectedProgramId' => $selectedProgramId,
            'perPage' => $perPage,
            'summary' => [
                'teachers' => $summaryAssignments->count(),
                'assigned_hours' => $summaryAssignments->sum('assigned_hours'),
                'registered_ideas' => $summaryAssignments->sum('registered_ideas'),
                'missing_ideas' => $summaryAssignments->sum('missing_ideas'),
            ],
            'reportModules' => $this->professorProjectionReportModules(),
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
    protected function professorProjectionReportModules(): array
    {
        return [
            'missing_ideas_by_professor' => [
                'label' => 'Faltantes por docente',
                'description' => 'Prioriza a los docentes cuya expectativa de ideas aun no se cumple en el periodo seleccionado.',
            ],
            'assigned_hours_by_professor' => [
                'label' => 'Horas y productividad',
                'description' => 'Contrasta horas asignadas, ideas esperadas y registradas por docente.',
            ],
            'program_balance' => [
                'label' => 'Balance por programa',
                'description' => 'Consolida carga, productividad y brecha de ideas a nivel de programa academico.',
            ],
        ];
    }

    protected function buildProfessorProjectionReportState(
        Request $request,
        Collection $assignments,
        Collection $periods,
        Collection $programs,
        ?int $selectedPeriodId,
        ?int $selectedProgramId
    ): array {
        $filters = $request->validate([
            'report_key' => ['nullable', Rule::in(array_keys($this->professorProjectionReportModules()))],
            'report_export' => ['nullable', Rule::in(['pdf'])],
        ]);

        $reportKey = $filters['report_key'] ?? 'missing_ideas_by_professor';
        $reportState = $this->composeProfessorProjectionReportState($reportKey, $assignments);

        return [
            'filters' => [
                'report_key' => $reportKey,
                'academic_period_id' => $selectedPeriodId,
                'program_id' => $selectedProgramId,
            ],
            'reportKey' => $reportKey,
            'reportLabel' => $this->professorProjectionReportModules()[$reportKey]['label'] ?? 'Reporte de proyeccion docente',
            'reportData' => $reportState['reportData'],
            'insights' => $reportState['insights'],
            'visuals' => $reportState['visuals'],
            'table' => $reportState['table'],
            'export' => $filters['report_export'] ?? null,
        ];
    }

    protected function composeProfessorProjectionReportState(string $reportKey, Collection $assignments): array
    {
        return match ($reportKey) {
            'assigned_hours_by_professor' => $this->buildProfessorHoursReportState($assignments),
            'program_balance' => $this->buildProfessorProgramBalanceReportState($assignments),
            default => $this->buildProfessorMissingIdeasReportState($assignments),
        };
    }

    protected function buildProfessorMissingIdeasReportState(Collection $assignments): array
    {
        $missingIdeas = $this->countsByProfessor($assignments, 'missing_ideas');
        $expectedIdeas = $this->countsByProfessor($assignments, 'expected_ideas');
        $registeredIdeas = $this->countsByProfessor($assignments, 'registered_ideas');
        $mostCritical = $missingIdeas->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
            'insights' => [
                [
                    'label' => 'Docentes con carga',
                    'value' => (string) $assignments->count(),
                    'caption' => 'Docentes que tienen una asignacion registrada en el periodo filtrado.',
                ],
                [
                    'label' => 'Ideas faltantes',
                    'value' => (string) $assignments->sum('missing_ideas'),
                    'caption' => 'Brecha total entre ideas esperadas e ideas registradas.',
                ],
                [
                    'label' => 'Ideas registradas',
                    'value' => (string) $assignments->sum('registered_ideas'),
                    'caption' => 'Ideas que los docentes ya registraron para el periodo analizado.',
                ],
                [
                    'label' => 'Mayor faltante',
                    'value' => (string) ($mostCritical ?? 'Sin datos'),
                    'caption' => 'Docente con la brecha mas alta frente a su expectativa de ideas.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'professors-missing-ideas',
                    'Ideas faltantes por docente',
                    'Ordena a los docentes segun la brecha de ideas que aun deben registrar.',
                    $this->reports->reportDataFromLabelCounts($missingIdeas->all()),
                    'Ideas faltantes',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'professors-expected-ideas',
                    'Ideas esperadas por docente',
                    'Sirve como referencia del compromiso total asumido por cada docente.',
                    $this->reports->reportDataFromLabelCounts($expectedIdeas->all()),
                    'Ideas esperadas',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'professors-registered-ideas',
                    'Ideas registradas por docente',
                    'Permite contrastar el avance real del registro de ideas en el periodo.',
                    $this->reports->reportDataFromLabelCounts($registeredIdeas->all()),
                    'Ideas registradas',
                    'ideas'
                ),
            ],
            'table' => [
                'title' => 'Seguimiento por docente',
                'description' => 'Detalle operativo de horas, expectativa, avance y faltantes por docente.',
                'columns' => ['Docente', 'Programa', 'Horas', 'Ideas esperadas', 'Registradas', 'Faltantes'],
                'rows' => $assignments
                    ->sortByDesc('missing_ideas')
                    ->map(fn (ResearchStaffTeacherAssignment $assignment) => [
                        $this->professorLabel($assignment),
                        $assignment->program?->name ?? 'Sin programa',
                        (string) $assignment->assigned_hours,
                        (string) $assignment->expected_ideas,
                        (string) $assignment->registered_ideas,
                        (string) $assignment->missing_ideas,
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildProfessorHoursReportState(Collection $assignments): array
    {
        $assignedHours = $this->countsByProfessor($assignments, 'assigned_hours');
        $registeredIdeas = $this->countsByProfessor($assignments, 'registered_ideas');
        $balanceStatus = $assignments
            ->map(fn (ResearchStaffTeacherAssignment $assignment) => $this->professorBalanceLabel($assignment))
            ->countBy()
            ->sortDesc();
        $topHours = $assignedHours->keys()->first();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($assignedHours->all()),
            'insights' => [
                [
                    'label' => 'Horas asignadas',
                    'value' => (string) $assignments->sum('assigned_hours'),
                    'caption' => 'Carga semanal total asignada a docentes en el periodo filtrado.',
                ],
                [
                    'label' => 'Promedio por docente',
                    'value' => $assignments->count() > 0
                        ? number_format($assignments->avg('assigned_hours'), 1, '.', '')
                        : '0',
                    'caption' => 'Promedio de horas semanales por docente con asignacion.',
                ],
                [
                    'label' => 'Docente con mayor carga',
                    'value' => (string) ($topHours ?? 'Sin datos'),
                    'caption' => 'Docente que concentra mas horas dentro del corte actual.',
                ],
                [
                    'label' => 'Ideas registradas',
                    'value' => (string) $assignments->sum('registered_ideas'),
                    'caption' => 'Produccion actual de ideas frente a la carga asignada.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'professors-assigned-hours',
                    'Horas asignadas por docente',
                    'Ayuda a entender como se distribuye la carga semanal entre docentes.',
                    $this->reports->reportDataFromLabelCounts($assignedHours->all()),
                    'Horas asignadas',
                    'horas'
                ),
                $this->reports->makeVisual(
                    'professors-productivity',
                    'Ideas registradas por docente',
                    'Contrasta la productividad actual con la distribucion de carga asignada.',
                    $this->reports->reportDataFromLabelCounts($registeredIdeas->all()),
                    'Ideas registradas',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'professors-balance-status',
                    'Estado de cumplimiento',
                    'Clasifica rapidamente quienes cumplen, superan o quedan cortos frente a su expectativa.',
                    $this->reports->reportDataFromLabelCounts($balanceStatus->all()),
                    'Docentes clasificados',
                    'docentes'
                ),
            ],
            'table' => [
                'title' => 'Carga y productividad docente',
                'description' => 'Permite revisar la relacion entre horas asignadas y resultados de registro por docente.',
                'columns' => ['Docente', 'Horas', 'Ideas esperadas', 'Registradas', 'Faltantes', 'Estado'],
                'rows' => $assignments
                    ->sortByDesc('assigned_hours')
                    ->map(fn (ResearchStaffTeacherAssignment $assignment) => [
                        $this->professorLabel($assignment),
                        (string) $assignment->assigned_hours,
                        (string) $assignment->expected_ideas,
                        (string) $assignment->registered_ideas,
                        (string) $assignment->missing_ideas,
                        $this->professorBalanceLabel($assignment),
                    ])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function buildProfessorProgramBalanceReportState(Collection $assignments): array
    {
        $hoursByProgram = $assignments->groupBy(fn (ResearchStaffTeacherAssignment $assignment) => $assignment->program?->name ?? 'Sin programa')
            ->map(fn (Collection $items) => (int) $items->sum('assigned_hours'))
            ->sortDesc();
        $registeredByProgram = $assignments->groupBy(fn (ResearchStaffTeacherAssignment $assignment) => $assignment->program?->name ?? 'Sin programa')
            ->map(fn (Collection $items) => (int) $items->sum('registered_ideas'))
            ->sortDesc();
        $missingByProgram = $assignments->groupBy(fn (ResearchStaffTeacherAssignment $assignment) => $assignment->program?->name ?? 'Sin programa')
            ->map(fn (Collection $items) => (int) $items->sum('missing_ideas'))
            ->sortDesc();

        return [
            'reportData' => $this->reports->reportDataFromLabelCounts($hoursByProgram->all()),
            'insights' => [
                [
                    'label' => 'Programas con carga',
                    'value' => (string) $hoursByProgram->count(),
                    'caption' => 'Programas que registran asignaciones docentes en el corte actual.',
                ],
                [
                    'label' => 'Horas consolidadas',
                    'value' => (string) $assignments->sum('assigned_hours'),
                    'caption' => 'Suma total de horas asignadas al conjunto filtrado.',
                ],
                [
                    'label' => 'Ideas registradas',
                    'value' => (string) $assignments->sum('registered_ideas'),
                    'caption' => 'Total de ideas producidas por los docentes del corte.',
                ],
                [
                    'label' => 'Brecha consolidada',
                    'value' => (string) $assignments->sum('missing_ideas'),
                    'caption' => 'Ideas faltantes acumuladas al consolidar por programa.',
                ],
            ],
            'visuals' => [
                $this->reports->makeVisual(
                    'program-hours',
                    'Horas asignadas por programa',
                    'Permite ver donde se concentra la mayor parte de la carga docente.',
                    $this->reports->reportDataFromLabelCounts($hoursByProgram->all()),
                    'Horas por programa',
                    'horas'
                ),
                $this->reports->makeVisual(
                    'program-registered-ideas',
                    'Ideas registradas por programa',
                    'Resume el avance global de registro de ideas agrupado por programa.',
                    $this->reports->reportDataFromLabelCounts($registeredByProgram->all()),
                    'Ideas registradas',
                    'ideas'
                ),
                $this->reports->makeVisual(
                    'program-missing-ideas',
                    'Faltantes por programa',
                    'Identifica los programas con mayor brecha en su produccion esperada de ideas.',
                    $this->reports->reportDataFromLabelCounts($missingByProgram->all()),
                    'Ideas faltantes',
                    'ideas'
                ),
            ],
            'table' => [
                'title' => 'Balance consolidado por programa',
                'description' => 'Consolida las asignaciones docentes para evaluar la brecha por programa academico.',
                'columns' => ['Programa', 'Docentes', 'Horas', 'Ideas esperadas', 'Registradas', 'Faltantes'],
                'rows' => $assignments
                    ->groupBy(fn (ResearchStaffTeacherAssignment $assignment) => $assignment->program?->name ?? 'Sin programa')
                    ->map(function (Collection $items, string $programName): array {
                        return [
                            $programName,
                            (string) $items->count(),
                            (string) $items->sum('assigned_hours'),
                            (string) $items->sum('expected_ideas'),
                            (string) $items->sum('registered_ideas'),
                            (string) $items->sum('missing_ideas'),
                        ];
                    })
                    ->sortByDesc(fn (array $row) => (int) $row[5])
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function countsByProfessor(Collection $assignments, string $field): Collection
    {
        return $assignments
            ->mapWithKeys(fn (ResearchStaffTeacherAssignment $assignment) => [$this->professorLabel($assignment) => (int) $assignment->{$field}])
            ->sortDesc()
            ->take(12);
    }

    protected function professorLabel(ResearchStaffTeacherAssignment $assignment): string
    {
        $fullName = trim(($assignment->professor?->name ?? '') . ' ' . ($assignment->professor?->last_name ?? ''));
        $cityName = $assignment->professor?->cityProgram?->city?->name;

        return $cityName ? $fullName . ' - ' . $cityName : ($fullName ?: 'Sin docente');
    }

    protected function professorBalanceLabel(ResearchStaffTeacherAssignment $assignment): string
    {
        if ((int) $assignment->registered_ideas === 0) {
            return 'Sin registro';
        }

        if ((int) $assignment->missing_ideas === 0 && (int) $assignment->idea_balance > 0) {
            return 'Supera expectativa';
        }

        if ((int) $assignment->missing_ideas === 0) {
            return 'Cumple expectativa';
        }

        if ((int) $assignment->missing_ideas === 1) {
            return 'Faltante leve';
        }

        return 'Faltante alto';
    }

    /**
     * @return array<int, string>
     */
    protected function professorProjectionReportFiltersSummary(
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
