<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Content;
use App\Models\ContentVersion;
use App\Models\Professor;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectEvaluationController extends Controller
{
    /**
     * Displays pending projects for the authenticated committee leader.
     */
    public function index()
    {
        // Obtain the record of the professor associated with the authenticated user (committee leader)
        $professor = Professor::where('user_id', Auth::id())
            ->where('committee_leader', true)
            ->whereNull('deleted_at') // avoid soft-deleted teachers
            ->first();

        // Validate that it has city_program_id
        if (!$professor || !$professor->city_program_id) {
            abort(403, 'No se pudo determinar el programa del líder de comité.');
        }

        $cityProgramId = $professor->city_program_id;

        // Filter projects by city_program of the lead professor
        $projects = Project::whereHas('projectStatus', function ($query) {
                $query->where('name', 'Pendiente de aprobación');
            })
            ->where(function ($query) use ($cityProgramId) {
                $query->whereHas('students', function ($sub) use ($cityProgramId) {
                    $sub->where('city_program_id', $cityProgramId);
                })
                ->orWhereHas('professors', function ($sub) use ($cityProgramId) {
                    $sub->where('city_program_id', $cityProgramId);
                });
            })
            ->with([
                'projectStatus',
                'thematicArea.investigationLine',
                'versions.contentVersions.content',
                'contentFrameworkProjects.contentFramework.framework',
                'students',
                'professors'
            ])
            ->get();

        return view('projects.evaluation.index', compact('projects'));
    }

   // app/Http/Controllers/ProjectEvaluationController.php
    public function show(Project $project)
    {
        // Cargar relaciones necesarias
        $project->load([
            'thematicArea.investigationLine',
            'projectStatus',
            'professors.user', // Eager load the user to expose a reliable email address on the detail page.
            'professors.cityProgram.program', // Preload the program so committee leaders can see contextual data without extra queries.
            'students',
            'contentFrameworks.framework', // ← Añadido
            'versions' => static fn ($relation) => $relation
                ->with(['contentVersions.content'])
                ->orderByDesc('created_at'),
        ]);

        // Última versión del proyecto
        $latestVersion = $project->versions()->latest('created_at')->first();

        // Preparar contenidos de la versión (puede quedar vacío si no hay versión)
        $contentValues = [];

        if ($latestVersion) {
            foreach ($latestVersion->contentVersions as $cv) {
                $label = $cv->content->label ?? $cv->content->name ?? 'Campo';
                $contentValues[$label] = $cv->value ?? '-';
            }
        }

        // ---- Frameworks aplicados ----
        // Se obtienen desde la relación contentFrameworkProjects
        $frameworksSelected = $project->contentFrameworks;

        return view('projects.evaluation.show', compact('project', 'latestVersion', 'contentValues', 'frameworksSelected'));
    }


    public function evaluate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Aprobado,Rechazado,Devuelto para corrección',
            'comments' => 'nullable|string',
        ]);

        $statusName = $validated['status'];

        // 🔍 Detectar si el proyecto es de profesor o de estudiantes
        $isProfessorProject = $project->professors()->exists();
        $isStudentProject = !$isProfessorProject; // si no tiene profesores, es de estudiantes

        // 🧠 Si el estado asignado es Aprobado y el proyecto es de estudiantes → cambiar a Asignado
        if ($statusName === 'Aprobado' && $isStudentProject) {
            $statusName = 'Asignado';
        }

        // Buscar el estado final en BD
        $status = ProjectStatus::where('name', $statusName)->first();
        if (!$status) {
            return back()->with('error', "No se encontró el estado '$statusName'.");
        }

        // ✅ Actualizar estado del proyecto
        $project->update(['project_status_id' => $status->id]);

        // 📌 Si se devolvió para corrección, guardar comentarios
        if ($validated['status'] === 'Devuelto para corrección') {
            $latestVersion = $project->versions()->latest('created_at')->first();

            if ($latestVersion) {
                $commentContent = Content::where('name', 'Comentarios')
                    ->whereJsonContains('roles', 'committee_leader')
                    ->first();

                if ($commentContent) {
                    ContentVersion::create([
                        'version_id' => $latestVersion->id,
                        'content_id' => $commentContent->id,
                        'value' => $validated['comments'] ?? 'Sin comentarios',
                    ]);
                }
            }
        }

        return redirect()
            ->route('projects.evaluation.index')
            ->with('success', "Evaluación del proyecto '{$project->title}' enviada correctamente con estado: $statusName.");
    }

}
