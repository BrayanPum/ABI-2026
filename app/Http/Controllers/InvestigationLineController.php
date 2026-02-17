<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffInvestigationLine;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de líneas de investigación
 *
 * Maneja el CRUD completo de líneas de investigación con soft delete,
 * validaciones robustas, filtrado por grupo de investigación y logging.
 */
class InvestigationLineController extends Controller
{
    /**
     * Muestra el listado de líneas de investigación con filtros
     *
     * Permite filtrar por búsqueda de texto (nombre, descripción)
     * y por grupo de investigación.
     *
     * @param Request $request Parámetros de consulta
     * @return View Vista con listado paginado
     */
    public function index(Request $request): View
    {
        try {
            // Obtener parámetros de filtrado
            $search = $request->get('search');
            $researchGroupId = $request->get('research_group_id');
            $perPage = (int) $request->get('per_page', 10);
            $perPage = $perPage > 0 ? min($perPage, 100) : 10;

            // Query base con relaciones y contadores
            $investigationLines = ResearchStaffInvestigationLine::query()
                ->with(['researchGroup'])
                ->withCount('thematicAreas')
                ->when($search, function ($query, string $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->when($researchGroupId, function ($query, $groupId) {
                    $query->where('research_group_id', $groupId);
                })
                ->orderBy('name')
                ->paginate($perPage)
                ->appends($request->query());

            // Obtener grupos de investigación para filtro
            $researchGroups = ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id');

            return view('investigation-lines.index', [
                'investigationLines' => $investigationLines,
                'researchGroups' => $researchGroups,
                'search' => $search,
                'researchGroupId' => $researchGroupId,
                'perPage' => $perPage,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar líneas de investigación: ' . $e->getMessage());

            return view('investigation-lines.index', [
                'investigationLines' => collect(),
                'researchGroups' => collect(),
                'search' => '',
                'researchGroupId' => null,
                'perPage' => 10,
                'error' => 'Ocurrió un error al cargar las líneas de investigación.',
            ]);
        }
    }

    /**
     * Muestra el formulario para crear una nueva línea de investigación
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        return view('investigation-lines.create', [
            'investigationLine' => new ResearchStaffInvestigationLine(),
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Almacena una nueva línea de investigación en la base de datos
     *
     * @param Request $request Datos del formulario
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validar datos de entrada
            $data = $request->validate([
                'name' => 'required|string|max:100|unique:investigation_lines,name',
                'description' => 'required|string|min:10',
                'research_group_id' => 'required|exists:research_groups,id',
            ], [
                'name.required' => 'El nombre de la línea de investigación es obligatorio.',
                'name.unique' => 'Ya existe una línea de investigación con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigación.',
                'research_group_id.exists' => 'El grupo de investigación seleccionado no es válido.',
            ]);

            return DB::transaction(function () use ($data) {
                // Crear línea de investigación
                $investigationLine = ResearchStaffInvestigationLine::create($data);

                // Registrar evento en logs
                Log::info('Línea de investigación creada', [
                    'investigation_line_id' => $investigationLine->id,
                    'investigation_line_name' => $investigationLine->name,
                    'research_group_id' => $investigationLine->research_group_id,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('investigation-lines.index')
                    ->with('success', "Línea de investigación '{$investigationLine->name}' creada correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear línea de investigación: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear la línea de investigación.')
                ->withInput();
        }
    }

    /**
     * Muestra el detalle de una línea de investigación
     *
     * @param InvestigationLine $investigationLine Línea a mostrar
     * @return View Vista con detalle
     */
    public function show(ResearchStaffInvestigationLine $investigationLine): View
    {
        // Verificar si fue eliminada
        if ($investigationLine->trashed()) {
            abort(404, 'La línea de investigación no está disponible.');
        }

        // Cargar relaciones
        $investigationLine->load(['researchGroup', 'thematicAreas' => fn ($query) => $query->orderBy('name')]);

        return view('investigation-lines.show', compact('investigationLine'));
    }

    /**
     * Muestra el formulario para editar una línea de investigación
     *
     * @param InvestigationLine $investigationLine Línea a editar
     * @return View Vista del formulario de edición
     */
    public function edit(ResearchStaffInvestigationLine $investigationLine): View
    {
        // Verificar si fue eliminada
        if ($investigationLine->trashed()) {
            abort(404, 'La línea de investigación no está disponible.');
        }

        return view('investigation-lines.edit', [
            'investigationLine' => $investigationLine,
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Actualiza una línea de investigación existente
     *
     * @param Request $request Datos del formulario
     * @param InvestigationLine $investigationLine Línea a actualizar
     * @return RedirectResponse Redirección con mensaje
     */
    public function update(Request $request, ResearchStaffInvestigationLine $investigationLine): RedirectResponse
    {
        try {
            // Validar datos excluyendo la línea actual en unicidad
            $data = $request->validate([
                'name' => 'required|string|max:100|unique:investigation_lines,name,' . $investigationLine->id,
                'description' => 'required|string|min:10',
                'research_group_id' => 'required|exists:research_groups,id',
            ], [
                'name.required' => 'El nombre de la línea de investigación es obligatorio.',
                'name.unique' => 'Ya existe otra línea de investigación con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigación.',
                'research_group_id.exists' => 'El grupo de investigación seleccionado no es válido.',
            ]);

            return DB::transaction(function () use ($investigationLine, $data) {
                // Verificar si fue eliminada
                if ($investigationLine->trashed()) {
                    return redirect()->route('investigation-lines.index')
                        ->with('error', 'No se puede actualizar una línea de investigación eliminada.');
                }

                // Actualizar
                $investigationLine->update($data);

                // Registrar evento en logs
                Log::info('Línea de investigación actualizada', [
                    'investigation_line_id' => $investigationLine->id,
                    'investigation_line_name' => $investigationLine->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('investigation-lines.index')
                    ->with('success', "Línea de investigación '{$investigationLine->name}' actualizada correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar línea de investigación: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar la línea de investigación.')
                ->withInput();
        }
    }

    /**
     * Elimina lógicamente (soft delete) una línea de investigación
     *
     * Verifica que no tenga áreas temáticas asociadas antes de eliminar.
     *
     * @param InvestigationLine $investigationLine Línea a eliminar
     * @return RedirectResponse Redirección con mensaje
     */
    public function destroy(ResearchStaffInvestigationLine $investigationLine): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($investigationLine) {
                // Verificar si ya fue eliminada
                if ($investigationLine->trashed()) {
                    return redirect()->route('investigation-lines.index')
                        ->with('error', 'La línea de investigación ya fue eliminada.');
                }

                // Verificar si tiene áreas temáticas asociadas
                if ($investigationLine->thematicAreas()->exists()) {
                    return redirect()->route('investigation-lines.index')
                        ->with('error', 'No se puede eliminar la línea porque tiene áreas temáticas asociadas.');
                }

                $name = $investigationLine->name;

                // Realizar soft delete
                $investigationLine->delete();

                // Registrar evento en logs
                Log::info('Línea de investigación eliminada', [
                    'investigation_line_id' => $investigationLine->id,
                    'investigation_line_name' => $name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('investigation-lines.index')
                    ->with('success', "Línea de investigación '{$name}' eliminada correctamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar línea de investigación: ' . $e->getMessage());

            return redirect()->route('investigation-lines.index')
                ->with('error', 'Ocurrió un error al eliminar la línea de investigación.');
        }
    }

    /**
     * Restaura una línea de investigación eliminada
     *
     * @param int $id ID de la línea a restaurar
     * @return RedirectResponse Redirección con mensaje
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar línea incluyendo eliminadas
                $investigationLine = ResearchStaffInvestigationLine::withTrashed()->findOrFail($id);

                // Verificar si está eliminada
                if (!$investigationLine->trashed()) {
                    return redirect()->route('investigation-lines.index')
                        ->with('error', 'La línea de investigación no está eliminada.');
                }

                // Restaurar
                $investigationLine->restore();

                // Registrar evento en logs
                Log::info('Línea de investigación restaurada', [
                    'investigation_line_id' => $investigationLine->id,
                    'investigation_line_name' => $investigationLine->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('investigation-lines.index')
                    ->with('success', "Línea de investigación '{$investigationLine->name}' restaurada correctamente.");
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('investigation-lines.index')
                ->with('error', 'No se encontró la línea de investigación especificada.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar línea de investigación: ' . $e->getMessage());

            return redirect()->route('investigation-lines.index')
                ->with('error', 'Ocurrió un error al restaurar la línea de investigación.');
        }
    }
}
