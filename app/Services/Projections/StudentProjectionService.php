<?php

namespace App\Services\Projections;

use App\Models\ResearchStaff\ResearchStaffAcademicPeriod;
use App\Models\ResearchStaff\ResearchStaffProject;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Services\Students\StudentAcademicProgressService;
use Illuminate\Support\Collection;

class StudentProjectionService
{
    private const TERMINAL_STATUSES = ['Rechazado', 'Descartado'];

    /**
     * @var array<int|string, \Illuminate\Support\Collection<int, array<string, mixed>>>
     */
    private array $supportRows = [];

    private ?array $continuitySuggestions = null;

    private ?Collection $loadedStudents = null;

    public function __construct(
        private readonly ProjectionPeriodService $periods,
        private readonly StudentAcademicProgressService $academicProgress
    )
    {
    }

    public function stageOptions(): array
    {
        return $this->academicProgress->stageOptions();
    }

    public function supportRows(?int $programId = null, ?int $academicPeriodId = null): Collection
    {
        $rows = $this->allSupportRows($academicPeriodId);

        if ($programId) {
            return $rows->where('program_id', $programId)->values();
        }

        return $rows;
    }

    public function projectedPg2StudentsForProgram(int $programId, ?int $academicPeriodId = null): int
    {
        return $this->supportRows($programId, $academicPeriodId)
            ->filter(fn (array $row) => $row['is_active'] && $row['projected_pg2_next_period'])
            ->count();
    }

    public function continuitySuggestionsByProgram(): array
    {
        if ($this->continuitySuggestions !== null) {
            return $this->continuitySuggestions;
        }

        $activePeriod = $this->periods->activePeriod();

        if (! $activePeriod) {
            return $this->continuitySuggestions = [];
        }

        $projects = ResearchStaffProject::query()
            ->with([
                'projectStatus',
                'students.user',
                'students.cityProgram.program',
                'professors.user',
                'professors.cityProgram.city',
                'professors.cityProgram.program',
            ])
            ->where('assignment_academic_period_id', $activePeriod->id)
            ->get()
            ->filter(fn ($project) => ! in_array($project->projectStatus?->name, self::TERMINAL_STATUSES, true))
            ->values();

        $suggestions = [];

        foreach ($projects as $project) {
            $activeStudents = $project->students
                ->filter(fn ($student) => ($student->user?->state ?? false) && ! $student->trashed());

            $programIds = $activeStudents
                ->pluck('cityProgram.program_id')
                ->filter()
                ->unique()
                ->values();

            if ($programIds->isEmpty()) {
                $programIds = $project->professors
                    ->pluck('cityProgram.program_id')
                    ->filter()
                    ->unique()
                    ->values();
            }

            foreach ($programIds as $programId) {
                foreach ($project->professors as $professor) {
                    if (! ($professor->user?->state ?? false) || $professor->trashed()) {
                        continue;
                    }

                    if ((int) ($professor->cityProgram?->program_id ?? 0) !== (int) $programId) {
                        continue;
                    }

                    $professorId = (int) $professor->id;
                    $cityName = $professor->cityProgram?->city?->name;
                    $fullName = trim(($professor->name ?? '') . ' ' . ($professor->last_name ?? ''));

                    if (! isset($suggestions[$programId][$professorId])) {
                        $suggestions[$programId][$professorId] = [
                            'professor_id' => $professorId,
                            'name' => $fullName,
                            'city' => $cityName,
                            'group_count' => 0,
                            'student_count' => 0,
                        ];
                    }

                    $suggestions[$programId][$professorId]['group_count']++;
                    $suggestions[$programId][$professorId]['student_count'] += $activeStudents
                        ->where('cityProgram.program_id', $programId)
                        ->count();
                }
            }
        }

        foreach ($suggestions as $programId => $programSuggestions) {
            uasort($programSuggestions, function (array $left, array $right): int {
                return [$right['group_count'], $right['student_count'], $left['name']]
                    <=> [$left['group_count'], $left['student_count'], $right['name']];
            });

            $suggestions[$programId] = $programSuggestions;
        }

        return $this->continuitySuggestions = $suggestions;
    }

    public function referencePeriod(?int $academicPeriodId = null): ?ResearchStaffAcademicPeriod
    {
        if ($academicPeriodId) {
            return ResearchStaffAcademicPeriod::query()->find($academicPeriodId);
        }

        return $this->periods->activePeriod();
    }

    private function allSupportRows(?int $academicPeriodId = null): Collection
    {
        $cacheKey = $academicPeriodId ?: 'default';

        if (array_key_exists($cacheKey, $this->supportRows)) {
            return $this->supportRows[$cacheKey];
        }

        $referencePeriod = $this->referencePeriod($academicPeriodId);

        $students = $this->loadedStudents ??= ResearchStaffStudent::query()
            ->with([
                'user',
                'cityProgram.city',
                'cityProgram.program',
                'projects' => fn ($query) => $query
                    ->with([
                        'projectStatus',
                        'assignmentAcademicPeriod',
                        'proposalAcademicPeriod',
                        'professors.user',
                        'professors.cityProgram.city',
                        'professors.cityProgram.program',
                    ])
                    ->orderByDesc('assigned_at')
                    ->orderByDesc('created_at'),
            ])
            ->orderBy('last_name')
            ->orderBy('name')
            ->get();

        return $this->supportRows[$cacheKey] = $students
            ->map(fn (ResearchStaffStudent $student) => $this->mapStudent($student, $referencePeriod))
            ->values();
    }

    private function mapStudent(ResearchStaffStudent $student, ?ResearchStaffAcademicPeriod $referencePeriod): array
    {
        $progress = $this->academicProgress->describeForPeriod($student, $referencePeriod);
        $assignedProject = $progress['assigned_project'];
        $latestProject = $progress['latest_project'];
        $teacherNames = $latestProject
            ? $latestProject->professors
                ->map(fn ($professor) => trim(($professor->name ?? '') . ' ' . ($professor->last_name ?? '')))
                ->filter()
                ->unique()
                ->implode(', ')
            : null;

        return [
            'student' => $student,
            'id' => (int) $student->id,
            'full_name' => trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')),
            'card_id' => $student->card_id,
            'semester' => (int) ($student->semester ?? 0),
            'program_id' => $student->cityProgram?->program?->id,
            'program_name' => $student->cityProgram?->program?->name,
            'city_name' => $student->cityProgram?->city?->name,
            'is_active' => (bool) ($student->user?->state ?? false) && ! $student->trashed(),
            'pg_stage_key' => $progress['stage_key'],
            'pg_stage_label' => $progress['stage_label'],
            'progression_note' => $progress['progression_note'],
            'project_title' => $latestProject?->title,
            'project_status' => $latestProject?->projectStatus?->name,
            'assignment_period_name' => $assignedProject?->assignmentAcademicPeriod?->name,
            'reference_period_name' => $referencePeriod?->name,
            'teacher_names' => $teacherNames,
            'projected_pg2_next_period' => (bool) $progress['projected_pg2_next_period'],
        ];
    }
}
