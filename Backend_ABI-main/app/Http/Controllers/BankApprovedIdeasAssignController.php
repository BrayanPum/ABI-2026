<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Student;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BankApprovedIdeasAssignController extends Controller
{
    /**
     * Muestra la pantalla para seleccionar compañeros antes de tomar una idea aprobada.
     */
    public function select(Project $project)
    {
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Validar que el proyecto sea elegible
        if ($project->projectStatus?->name !== 'Aprobado') {
            abort(403, 'Solo puedes seleccionar ideas que estén en estado Aprobado.');
        }

        // Verificar que el estudiante no esté bloqueado por otro proyecto
        $currentStatus = $student->projects()->latest()->first()?->projectStatus?->name;

        if (in_array($currentStatus, ['Asignado', 'Aprobado', 'Devuelto para corrección', 'Pendiente de aprobación'])) {
            abort(403, 'Ya tienes un proyecto activo. No puedes seleccionar uno nuevo.');
        }

        // Obtener compañeros elegibles (solo mismo city_program)
        $availableStudents = Student::query()
            ->where('city_program_id', $student->city_program_id)
            ->where('id', '!=', $student->id)
            ->where(function ($q) {
                $q->whereDoesntHave('projects') // sin proyectos
                ->orWhere(function ($q2) {
                    $q2->whereHas('projects', fn($p) =>
                            $p->whereHas('projectStatus', fn($s) =>
                                $s->where('name', 'Rechazado')
                            )
                        )
                        ->whereDoesntHave('projects', fn($p) =>
                            $p->whereHas('projectStatus', fn($s) =>
                                $s->whereNot('name', 'Rechazado')
                            )
                        );
                });
            })
            ->orderBy('last_name')
            ->orderBy('name')
            ->get();

        return view('projects.student.select', compact('project', 'availableStudents'));
    }

    /**
     * Procesa la asignación del proyecto al estudiante y su equipo.
     */
    public function assign(Request $request, Project $project)
    {
        $student = Student::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        $validated = $request->validate([
            'teammate_ids' => ['nullable', 'array', 'max:2'],
            'teammate_ids.*' => [
                'integer',
                Rule::exists('students', 'id')
                    ->where(fn ($q) => $q->where('city_program_id', $student->city_program_id))
            ],
        ]);

        // Validar que el proyecto esté disponible
        if ($project->projectStatus?->name !== 'Aprobado') {
            abort(403, 'Este proyecto ya no está disponible para asignación.');
        }

        // Estados permitidos para tomar otro proyecto
        $allowedStatuses = ['Rechazado', 'Devuelto para corrección'];

        // 1️⃣ Validar que el estudiante NO tenga otro proyecto activo
        $hasActiveUser = $student->projects()
            ->where('projects.id', '!=', $project->id)
            ->whereHas('projectStatus', fn($status) =>
                $status->whereNotIn('name', $allowedStatuses)
            )
            ->exists();

        if ($hasActiveUser) {
            return back()
                ->with('error', 'No puedes asignarte a este proyecto porque ya tienes un proyecto activo.');
        }

        // 2️⃣ Validar compañeros seleccionados
        if (!empty($validated['teammate_ids'])) {
            $hasActiveTeammates = Student::query()
                ->whereIn('id', $validated['teammate_ids'])
                ->whereHas('projects', fn($query) =>
                    $query->where('projects.id', '!=', $project->id)
                        ->whereHas('projectStatus', fn($status) =>
                            $status->whereNotIn('name', $allowedStatuses)
                        )
                )
                ->exists();

            if ($hasActiveTeammates) {
                return back()
                    ->with('error', 'Uno o más compañeros seleccionados ya tienen un proyecto activo.');
            }
        }

        DB::beginTransaction();

        try {
            // Cambiar estado del proyecto a “Asignado”
            $assignedStatusId = ProjectStatus::where('name', 'Asignado')->first()->id;
            $project->update(['project_status_id' => $assignedStatusId]);

            // Vincular estudiantes
            $studentIds = collect($validated['teammate_ids'] ?? [])
                ->push($student->id)
                ->unique()
                ->values()
                ->all();

            $project->students()->syncWithoutDetaching($studentIds);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al asignar el proyecto. Intenta de nuevo.');
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyecto asignado exitosamente. Ahora es tuyo para ejecutar.');
    }
}
