<?php

namespace App\Http\Controllers;

use App\Helpers\AuthUserHelper;
use App\Models\Professor;
use App\Models\Project;
use App\Models\Student;
use App\Models\User;
use App\Models\Version;
use Illuminate\View\View;

class ProjectVersionController extends Controller
{
    /**
     * Display the version history associated with the project.
     */
    public function index(Project $project): View
    {
        $user = AuthUserHelper::fullUser();
        $this->authorizeHistoryAccess($project, $user);

        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'professors.user',
            'students.user',
        ]);

        $totalVersions = Version::query()->where('project_id', $project->id)->count();

        $versions = Version::query()
            ->with(['createdBy.professor', 'createdBy.student', 'createdBy.researchstaff'])
            ->where('project_id', $project->id)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $versions->setCollection(
            $versions->getCollection()->values()->map(function (Version $version, int $index) use ($versions, $project, $totalVersions) {
                $position = (($versions->currentPage() - 1) * $versions->perPage()) + $index;
                $snapshot = $this->resolveSnapshot($project, $version);

                $version->history_number = max($totalVersions - $position, 1);
                $version->history_author = $this->resolveAuthorLabel($version);
                $version->history_status = data_get($snapshot, 'project_status.name', 'Sin estado');
                $version->history_contents_count = count($snapshot['contents'] ?? []);
                $version->history_frameworks_count = count($snapshot['frameworks'] ?? []);

                return $version;
            })
        );

        return view('project-versions.index', [
            'project' => $project,
            'versions' => $versions,
            'totalVersions' => $totalVersions,
        ]);
    }

    /**
     * Display an informational screen because versions are generated automatically.
     */
    public function create(Project $project): View
    {
        $user = AuthUserHelper::fullUser();
        $this->authorizeHistoryAccess($project, $user);

        $project->load(['projectStatus', 'thematicArea.investigationLine']);

        return view('project-versions.create', [
            'project' => $project,
        ]);
    }

    /**
     * Show the full snapshot of a project version.
     */
    public function show(Project $project, Version $version): View
    {
        $user = AuthUserHelper::fullUser();
        $this->authorizeHistoryAccess($project, $user);

        if ((int) $version->project_id !== (int) $project->id) {
            abort(404);
        }

        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'professors.user',
            'students.user',
            'contentFrameworks.framework',
        ]);

        $version->load(['createdBy.professor', 'createdBy.student', 'createdBy.researchstaff', 'contentVersions.content']);

        $snapshot = $this->resolveSnapshot($project, $version);
        $totalVersions = Version::query()->where('project_id', $project->id)->count();
        $historyNumber = Version::query()
            ->where('project_id', $project->id)
            ->where('id', '<=', $version->id)
            ->count();

        return view('project-versions.show', [
            'project' => $project,
            'version' => $version,
            'snapshot' => $snapshot,
            'totalVersions' => $totalVersions,
            'historyNumber' => $historyNumber,
            'authorLabel' => $this->resolveAuthorLabel($version),
        ]);
    }

    /**
     * Display an informational screen because versions are read-only.
     */
    public function edit(Project $project, Version $version): View
    {
        $user = AuthUserHelper::fullUser();
        $this->authorizeHistoryAccess($project, $user);

        if ((int) $version->project_id !== (int) $project->id) {
            abort(404);
        }

        return view('project-versions.edit', [
            'project' => $project,
            'version' => $version,
        ]);
    }

    /**
     * Restrict the version history to the project's own participants, research staff,
     * and committee leaders evaluating projects within their own program.
     */
    protected function authorizeHistoryAccess(Project $project, ?User $user): void
    {
        if (! $user) {
            abort(403);
        }

        if ($user->role === 'research_staff') {
            return;
        }

        $project->loadMissing(['professors', 'students']);

        if (in_array($user->role, ['professor', 'committee_leader'], true)) {
            $professor = $user->professor ?? Professor::query()->where('user_id', $user->id)->first();

            if ($professor && $project->professors->contains('id', $professor->id)) {
                return;
            }

            if ($user->role === 'committee_leader' && $professor && $professor->committee_leader && $professor->city_program_id) {
                $belongsToProgram = $project->professors->contains('city_program_id', $professor->city_program_id)
                    || $project->students->contains('city_program_id', $professor->city_program_id);

                if ($belongsToProgram) {
                    return;
                }
            }

            abort(403, 'No estás asignado a este proyecto.');
        }

        if ($user->role === 'student') {
            $student = $user->student;

            if ($student && $project->students->contains('id', $student->id)) {
                return;
            }

            abort(403, 'No estás asignado a este proyecto.');
        }

        abort(403, 'Acceso no autorizado.');
    }

    /**
     * Build a compatible snapshot for legacy and new versions.
     */
    protected function resolveSnapshot(Project $project, Version $version): array
    {
        if (is_array($version->snapshot) && ! empty($version->snapshot)) {
            return $version->snapshot;
        }

        $contents = $version->relationLoaded('contentVersions')
            ? $version->contentVersions
            : $version->contentVersions()->with('content')->get();

        return [
            'title' => $project->title,
            'evaluation_criteria' => $project->evaluation_criteria,
            'project_status' => [
                'id' => $project->projectStatus?->id,
                'name' => $project->projectStatus?->name,
            ],
            'thematic_area' => [
                'id' => $project->thematicArea?->id,
                'name' => $project->thematicArea?->name,
            ],
            'investigation_line' => [
                'id' => $project->thematicArea?->investigationLine?->id,
                'name' => $project->thematicArea?->investigationLine?->name,
            ],
            'contents' => $contents
                ->filter(static fn ($contentVersion) => $contentVersion->content !== null)
                ->mapWithKeys(function ($contentVersion) {
                    return [$this->contentDisplayName($contentVersion->content->name) => $contentVersion->value];
                })
                ->toArray(),
            'frameworks' => $project->contentFrameworks
                ->map(function ($contentFramework) {
                    return [
                        'id' => $contentFramework->id,
                        'name' => $contentFramework->name,
                        'framework' => [
                            'id' => $contentFramework->framework?->id,
                            'name' => $contentFramework->framework?->name,
                        ],
                    ];
                })
                ->values()
                ->all(),
            'participants' => [
                'professors' => $project->professors
                    ->map(function (Professor $professor) {
                        return [
                            'id' => $professor->id,
                            'name' => trim(($professor->name ?? '') . ' ' . ($professor->last_name ?? '')),
                            'email' => $professor->mail ?? $professor->user?->email,
                        ];
                    })
                    ->values()
                    ->all(),
                'students' => $project->students
                    ->map(function (Student $student) {
                        return [
                            'id' => $student->id,
                            'name' => trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')),
                            'card_id' => $student->card_id,
                        ];
                    })
                    ->values()
                    ->all(),
            ],
        ];
    }

    /**
     * Present readable labels even if the catalog data is stored without accents.
     */
    protected function contentDisplayName(?string $name): string
    {
        $normalizedName = str($name)->ascii()->lower()->replace('-', ' ')->squish()->toString();

        return [
            'titulo' => 'Titulo',
            'cantidad de estudiantes' => 'Cantidad de estudiantes',
            'tiempo de ejecucion' => 'Tiempo de ejecucion',
            'viabilidad' => 'Viabilidad',
            'pertinencia con el grupo de investigacion y con el programa' => 'Pertinencia con el grupo de investigacion y con el programa',
            'disponibilidad de docentes para su direccion y calificacion' => 'Disponibilidad de docentes para su direccion y calificacion',
            'calidad y correspondencia entre titulo y objetivo' => 'Calidad y correspondencia entre titulo y objetivo',
            'objetivo general del proyecto' => 'Objetivo general del proyecto',
            'descripcion del proyecto de investigacion' => 'Descripcion del proyecto de investigacion',
            'comentarios' => 'Comentarios',
        ][$normalizedName] ?? (string) $name;
    }

    /**
     * Resolve the best human-readable label for the user who generated the version.
     */
    protected function resolveAuthorLabel(Version $version): string
    {
        $user = $version->createdBy;
        if (! $user) {
            return 'No disponible';
        }

        if ($user->student) {
            return trim($user->student->name . ' ' . $user->student->last_name) . ' (Student)';
        }

        if ($user->professor) {
            return trim($user->professor->name . ' ' . $user->professor->last_name) . ' (Professor)';
        }

        if ($user->researchstaff) {
            return trim($user->researchstaff->name . ' ' . $user->researchstaff->last_name) . ' (Research staff)';
        }

        return $user->email;
    }
}
