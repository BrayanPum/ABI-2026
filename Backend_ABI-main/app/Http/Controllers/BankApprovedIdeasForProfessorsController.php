<?php

namespace App\Http\Controllers;
use App\Models\Professor;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BankApprovedIdeasForProfessorsController extends Controller
{
    /**
     * Muestra los proyectos aprobados relacionados con el estudiante autenticado.
     */
    public function index(Request $request)
    {
        // 1️⃣ Obtener el profesor autenticado
        $professor = Professor::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->first();

        if (!$professor || !$professor->city_program_id) {
            $perPage = $request->input('per_page', 10);
            $thematicAreas = collect();
            $projects = Project::whereRaw('1 = 0')->paginate($perPage);
            return view('projects.professor.approved', [
                'projects' => $projects,
                'thematicAreas' => $thematicAreas,
                'thematicAreaId' => null,
                'perPage' => $perPage,
            ])->with('error', 'Completa tu asignación de programa para ver proyectos aprobados.');
        }

        $perPage = $request->input('per_page', 10);
        $cityProgramId = $professor->city_program_id;
        $thematicAreaId = $request->input('thematic_area_id');

        // Obtener CityProgram → Programa → Grupo de investigación
        $cityProgram = \App\Models\CityProgram::find($cityProgramId);
        $program = $cityProgram?->program;
        $researchGroup = $program?->researchGroup;

        // Áreas temáticas del grupo
        $thematicAreas = collect();
        if ($researchGroup) {
            $thematicAreas = \App\Models\ThematicArea::whereHas('investigationLine', function ($q) use ($researchGroup) {
                    $q->where('research_group_id', $researchGroup->id);
                })
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get();
        }

        // 🔥 SOLO proyectos aprobados creados por docentes del mismo City Program
        $projects = Project::whereHas('projectStatus', fn($q) => $q->where('name', 'Aprobado'))
            ->whereHas('professors', function ($q) use ($cityProgramId) {
                $q->where('city_program_id', $cityProgramId);
            })
            ->with([
                'projectStatus',
                'thematicArea.investigationLine',
                'versions.contentVersions.content',
                'contentFrameworkProjects.contentFramework.framework',
                'professors' // no se cargan students porque ya no son relevantes
            ])
            ->paginate($perPage);

        return view('projects.professor.approved', [
            'projects' => $projects,
            'thematicAreas' => $thematicAreas,
            'thematicAreaId' => $thematicAreaId,
            'perPage' => $perPage
        ]);
    }

    public function show(Project $project)
    {
        // Obtener el profesor autenticado
        $professor = Professor::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Verificar si el proyecto pertenece al mismo programa del profesor
        $sameProgram = $project->students()
                ->where('city_program_id', $professor->city_program_id)
                ->exists()
            || $project->professors()
                ->where('city_program_id', $professor->city_program_id)
                ->exists();

        if (! $sameProgram) {
            abort(403, 'No tienes permiso para ver este proyecto.');
        }

        // Cargar relaciones necesarias
        $project->load([
            'projectStatus',
            'thematicArea.investigationLine',
            'versions.contentVersions.content',
            'contentFrameworkProjects.contentFramework.framework',
            'students',
            'professors',
        ]);

        // Obtener la última versión del proyecto
        $latestVersion = $project->versions()->latest('created_at')->first();

        // Mapear contenidos para que la vista los muestre como label => valor
        $contentValues = [];
        if ($latestVersion) {
            $contentValues = $latestVersion->contentVersions
                ->mapWithKeys(function ($cv) {
                    return [$cv->content->name => $cv->value];
                })
                ->toArray();
        }

        // Marcos seleccionados
        $frameworksSelected = $project->contentFrameworkProjects()
            ->with('contentFramework.framework')
            ->get()
            ->map(function ($item) {
                return $item->contentFramework;
            });

        return view('projects.professor.show', compact(
            'project',
            'latestVersion',
            'contentValues',
            'frameworksSelected'
        ));
    }
}
