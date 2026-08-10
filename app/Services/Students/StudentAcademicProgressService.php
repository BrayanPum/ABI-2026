<?php

namespace App\Services\Students;

use App\Models\AcademicPeriod;
use App\Models\Project;
use App\Models\Student;
use App\Services\Projections\ProjectionPeriodService;

class StudentAcademicProgressService
{
    private const TERMINAL_STATUSES = ['Rechazado', 'Descartado'];

    public function __construct(private readonly ProjectionPeriodService $periods)
    {
    }

    public function stageOptions(): array
    {
        return [
            Student::PG_STAGE_PG1 => 'PG1 actual',
            Student::PG_STAGE_PG2 => 'PG2 actual',
        ];
    }

    public function syncAll(?AcademicPeriod $activePeriod = null): void
    {
        $activePeriod ??= $this->periods->activePeriod();

        Student::query()
            ->with([
                'projects' => fn ($query) => $query
                    ->with([
                        'projectStatus',
                        'assignmentAcademicPeriod',
                        'proposalAcademicPeriod',
                        'professors.user',
                    ])
                    ->orderByDesc('assigned_at')
                    ->orderByDesc('created_at'),
            ])
            ->chunkById(100, function ($students) use ($activePeriod): void {
                foreach ($students as $student) {
                    $stage = $this->determineStoredStage($student, $activePeriod);

                    if ($this->normalizedStoredStage($student) === $stage) {
                        continue;
                    }

                    Student::query()
                        ->whereKey($student->id)
                        ->update(['pg_stage' => $stage]);
                }
            });
    }

    public function syncStudent(Student $student, ?AcademicPeriod $activePeriod = null): string
    {
        $activePeriod ??= $this->periods->activePeriod();

        $this->loadStudentContext($student);

        $stage = $this->determineStoredStage($student, $activePeriod);

        if ($this->normalizedStoredStage($student) !== $stage) {
            Student::query()
                ->whereKey($student->id)
                ->update(['pg_stage' => $stage]);

            $student->setAttribute('pg_stage', $stage);
        }

        return $stage;
    }

    public function describe(Student $student, ?AcademicPeriod $activePeriod = null): array
    {
        $activePeriod ??= $this->periods->activePeriod();

        $this->loadStudentContext($student);

        $stageKey = $this->normalizedStoredStage($student);
        $assignedProject = $this->assignedProjectForStudent($student);
        $latestProject = $assignedProject ?? $this->latestNonTerminalProject($student);
        $blockingReason = $this->blockingReason($stageKey, $assignedProject, $latestProject);

        return [
            'stage_key' => $stageKey,
            'stage_label' => $this->stageOptions()[$stageKey],
            'progression_note' => $this->progressionNote($stageKey, $assignedProject, $latestProject, $activePeriod),
            'assigned_project' => $assignedProject,
            'latest_project' => $latestProject,
            'can_access_idea_bank' => $blockingReason === null,
            'can_create_proposal' => $blockingReason === null,
            'bank_block_reason' => $blockingReason,
            'projected_pg2_next_period' => $this->projectsToPg2NextPeriod($stageKey, $assignedProject, $activePeriod),
        ];
    }

    public function describeForPeriod(Student $student, ?AcademicPeriod $activePeriod = null): array
    {
        $activePeriod ??= $this->periods->activePeriod();

        $this->loadStudentContext($student);

        $stageKey = $this->determineStoredStage($student, $activePeriod);
        $assignedProject = $this->assignedProjectForStudent($student);
        $latestProject = $assignedProject ?? $this->latestNonTerminalProject($student);
        $blockingReason = $this->blockingReason($stageKey, $assignedProject, $latestProject);

        return [
            'stage_key' => $stageKey,
            'stage_label' => $this->stageOptions()[$stageKey],
            'progression_note' => $this->progressionNote($stageKey, $assignedProject, $latestProject, $activePeriod),
            'assigned_project' => $assignedProject,
            'latest_project' => $latestProject,
            'can_access_idea_bank' => $blockingReason === null,
            'can_create_proposal' => $blockingReason === null,
            'bank_block_reason' => $blockingReason,
            'projected_pg2_next_period' => $this->projectsToPg2NextPeriod($stageKey, $assignedProject, $activePeriod),
        ];
    }

    public function canAccessIdeaBank(Student $student, ?AcademicPeriod $activePeriod = null): bool
    {
        $this->syncStudent($student, $activePeriod);

        return $this->describe($student, $activePeriod)['can_access_idea_bank'];
    }

    public function canCreateProposal(Student $student, ?AcademicPeriod $activePeriod = null): bool
    {
        $this->syncStudent($student, $activePeriod);

        return $this->describe($student, $activePeriod)['can_create_proposal'];
    }

    public function blockedIdeaBankMessage(Student $student, ?AcademicPeriod $activePeriod = null): string
    {
        $this->syncStudent($student, $activePeriod);

        $progress = $this->describe($student, $activePeriod);

        return match ($progress['bank_block_reason']) {
            'pg2' => 'No puedes abrir el banco de proyectos porque ya cursas PG2 y debes continuar con la idea o proyecto que vienes trabajando.',
            'assigned_project' => 'No puedes abrir el banco de proyectos porque ya tienes un proyecto asignado en PG1.',
            'proposal_in_progress' => 'No puedes abrir el banco de proyectos porque ya tienes una idea o proyecto en tramite.',
            default => 'No puedes abrir el banco de proyectos en tu estado academico actual.',
        };
    }

    public function blockedProposalMessage(Student $student, ?AcademicPeriod $activePeriod = null): string
    {
        $this->syncStudent($student, $activePeriod);

        $progress = $this->describe($student, $activePeriod);

        return match ($progress['bank_block_reason']) {
            'pg2' => 'No puedes registrar una nueva idea porque ya cursas PG2 y debes continuar con la idea o proyecto que vienes trabajando.',
            'assigned_project' => 'No puedes registrar una nueva idea porque ya tienes un proyecto asignado en PG1.',
            'proposal_in_progress' => 'No puedes registrar una nueva idea porque ya tienes una idea o proyecto en tramite.',
            default => 'No puedes registrar una nueva idea en tu estado academico actual.',
        };
    }

    private function loadStudentContext(Student $student): void
    {
        $student->loadMissing([
            'projects' => fn ($query) => $query
                ->with([
                    'projectStatus',
                    'assignmentAcademicPeriod',
                    'proposalAcademicPeriod',
                    'professors.user',
                ])
                ->orderByDesc('assigned_at')
                ->orderByDesc('created_at'),
        ]);
    }

    private function normalizedStoredStage(Student $student): string
    {
        $stage = (string) ($student->pg_stage ?? Student::PG_STAGE_PG1);

        return in_array($stage, [Student::PG_STAGE_PG1, Student::PG_STAGE_PG2], true)
            ? $stage
            : Student::PG_STAGE_PG1;
    }

    private function determineStoredStage(Student $student, ?AcademicPeriod $activePeriod = null): string
    {
        if (! $activePeriod) {
            return $this->normalizedStoredStage($student);
        }

        $assignedProject = $this->assignedProjectForStudent($student);

        if (! $assignedProject) {
            return Student::PG_STAGE_PG1;
        }

        $assignmentPeriod = $assignedProject->assignmentAcademicPeriod;

        if ($assignmentPeriod?->start_date && $activePeriod->start_date && $assignmentPeriod->start_date->lt($activePeriod->start_date)) {
            return Student::PG_STAGE_PG2;
        }

        return Student::PG_STAGE_PG1;
    }

    private function blockingReason(string $stageKey, ?Project $assignedProject, ?Project $latestProject): ?string
    {
        if ($stageKey === Student::PG_STAGE_PG2) {
            return 'pg2';
        }

        if ($assignedProject) {
            return 'assigned_project';
        }

        if ($latestProject) {
            return 'proposal_in_progress';
        }

        return null;
    }

    private function progressionNote(
        string $stageKey,
        ?Project $assignedProject,
        ?Project $latestProject,
        ?AcademicPeriod $activePeriod = null
    ): string {
        if ($stageKey === Student::PG_STAGE_PG2) {
            if ($assignedProject?->assignmentAcademicPeriod?->name) {
                return 'Cursa PG2 porque su proyecto fue asignado en ' . $assignedProject->assignmentAcademicPeriod->name . ' y ya transcurrio un periodo academico.';
            }

            return 'Cursa PG2 segun el seguimiento academico almacenado para el estudiante.';
        }

        if ($assignedProject && $activePeriod && (int) $assignedProject->assignment_academic_period_id === (int) $activePeriod->id) {
            return 'Cursa PG1 y su proyecto fue asignado en el periodo academico activo.';
        }

        if ($assignedProject) {
            return 'Cursa PG1 y ya tiene un proyecto asignado.';
        }

        if ($latestProject) {
            return 'Cursa PG1 y ya tiene una idea o proyecto en tramite.';
        }

        return 'Cursa PG1 y aun no tiene un proyecto asignado.';
    }

    private function projectsToPg2NextPeriod(string $stageKey, ?Project $assignedProject, ?AcademicPeriod $activePeriod = null): bool
    {
        return $stageKey === Student::PG_STAGE_PG1
            && $assignedProject !== null
            && $activePeriod !== null
            && (int) $assignedProject->assignment_academic_period_id === (int) $activePeriod->id;
    }

    private function assignedProjectForStudent(Student $student): ?Project
    {
        return $student->projects
            ->filter(function (Project $project): bool {
                return $project->assignment_academic_period_id !== null
                    && ! in_array($project->projectStatus?->name, self::TERMINAL_STATUSES, true);
            })
            ->sortByDesc(function (Project $project): string {
                $periodTimestamp = $project->assignmentAcademicPeriod?->start_date?->getTimestamp() ?? 0;
                $assignedTimestamp = $project->assigned_at?->getTimestamp() ?? 0;

                return sprintf('%020d%020d%020d', $periodTimestamp, $assignedTimestamp, $project->id);
            })
            ->first();
    }

    private function latestNonTerminalProject(Student $student): ?Project
    {
        return $student->projects
            ->filter(fn (Project $project) => ! in_array($project->projectStatus?->name, self::TERMINAL_STATUSES, true))
            ->sortByDesc(function (Project $project): string {
                $createdTimestamp = $project->created_at?->getTimestamp() ?? 0;

                return sprintf('%020d%020d', $createdTimestamp, $project->id);
            })
            ->first();
    }
}
