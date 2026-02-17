<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Project;
use App\Models\ThematicArea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BankApprovedIdeasForStudentsController extends Controller
{
    /**
     * Muestra los proyectos aprobados relacionados con el estudiante autenticado.
     */
    public function index(Request $request)
    {
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->first();

        if (!$student || !$student->city_program_id) {
            abort(403, 'No se pudo determinar el programa académico del estudiante.');
        }

        $perPage = $request->input('per_page', 10);
        $thematicAreaId = $request->input('thematic_area_id');

        // Obtener grupo de investigación a través del programa del estudiante
        $program = $student->cityProgram?->program;
        $researchGroupId = $program?->research_group_id;

        if (!$researchGroupId) {
            abort(403, 'Tu programa académico no tiene un grupo de investigación asociado.');
        }

        // Obtener TODAS las áreas temáticas del grupo del estudiante
        $thematicAreas = ThematicArea::whereHas('investigationLine', function ($q) use ($researchGroupId) {
                $q->where('research_group_id', $researchGroupId);
            })
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        // --- QUERY PROYECTOS APROBADOS ---
        $projectsQuery = Project::whereHas('projectStatus', fn($q) => $q->where('name', 'Aprobado'))
            ->whereHas('professors', function ($q) use ($student) {
                $q->where('city_program_id', $student->city_program_id);
            });

        // Aplicar filtro dinámico
        if (!empty($thematicAreaId)) {
            $projectsQuery->where('thematic_area_id', $thematicAreaId);
        }

        $projects = $projectsQuery
            ->with([
                'projectStatus',
                'thematicArea.investigationLine',
                'versions.contentVersions.content',
                'contentFrameworkProjects.contentFramework.framework',
                'professors',
                'students'
            ])
            ->paginate($perPage)
            ->withQueryString(); // mantiene filtros entre páginas

        return view('projects.student.approved', [
            'projects' => $projects,
            'thematicAreas' => $thematicAreas,
            'thematicAreaId' => $thematicAreaId,
            'perPage' => $perPage
        ]);
    }


    public function show(Project $project)
    {
        // Obtener el estudiante autenticado
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Validar que pertenece al mismo programa
        $sameProgram = $project->students()
                ->where('city_program_id', $student->city_program_id)
                ->exists()
            || $project->professors()
                ->where('city_program_id', $student->city_program_id)
                ->exists();

        if (! $sameProgram) {
            abort(403, 'No tienes permiso para ver este proyecto.');
        }

        // Cargar relaciones
        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'versions.contentVersions.content',
            'contentFrameworkProjects.contentFramework.framework',
            'students',
            'professors'
        ]);

        // Última versión
        $latestVersion = $project->versions()->latest('created_at')->first();

        // Mapear contenidos para mostrar como label => valor
        $contentValues = [];
        if ($latestVersion) {
            $contentValues = $latestVersion->contentVersions
                ->mapWithKeys(fn($cv) => [$cv->content->name => $cv->value])
                ->toArray();
        }

        // Marcos seleccionados
        $frameworksSelected = $project->contentFrameworkProjects()
            ->with('contentFramework.framework')
            ->get()
            ->map(fn($item) => $item->contentFramework);

        /**
         * El estudiante NO podrá seleccionar un proyecto si ya tiene uno en estado "Asignado".
         */
        $hasAssignedProject = $student->projects()
            ->whereHas('projectStatus', fn($q) => $q->where('name', 'Asignado'))
            ->exists();

        // Si NO tiene proyecto asignado => puede ver botón (true)
        $canSelectProject = ! $hasAssignedProject;

        return view('projects.student.show', compact(
            'project',
            'latestVersion',
            'contentValues',
            'frameworksSelected',
            'canSelectProject'
        ));
    }

}
