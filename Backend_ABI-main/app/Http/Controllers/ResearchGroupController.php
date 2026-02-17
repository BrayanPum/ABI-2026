<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de grupos de investigación
 *
 * Maneja el CRUD completo de grupos de investigación con soft delete,
 * validaciones de nombre e iniciales únicas y logging de operaciones.
 */
class ResearchGroupController extends Controller
{
    /**
     * Muestra el listado de grupos de investigación con filtros
     *
     * Permite filtrar por búsqueda de texto (nombre, iniciales)
     * y muestra contadores de programas y líneas de investigación.
     *
     * @param Request $request Parámetros de consulta
     * @return View Vista con listado paginado
     */
    public function index(Request $request): View
    {
        try {
            // Obtener parámetros de filtrado
            $search = $request->get('search');
            $perPage = (int) $request->get('per_page', 10);
            $perPage = $perPage > 0 ? min($perPage, 100) : 10;

            // Query base con contadores de relaciones
            $researchGroups = ResearchStaffResearchGroup::query()
                ->withCount(['programs', 'investigationLines'])
                ->when($search, function ($query, string $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('initials', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate($perPage)
                ->appends($request->query());

            return view('research-groups.index', [
                'researchGroups' => $researchGroups,
                'search' => $search,
                'perPage' => $perPage,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar grupos de investigación: ' . $e->getMessage());

            return view('research-groups.index', [
                'researchGroups' => collect(),
                'search' => '',
                'perPage' => 10,
                'error' => 'Ocurrió un error al cargar los grupos de investigación.',
            ]);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo grupo de investigación
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        return view('research-groups.create', [
            'researchGroup' => new ResearchStaffResearchGroup(),
        ]);
    }

    /**
     * Almacena un nuevo grupo de investigación en la base de datos
     *
     * @param Request $request Datos del formulario
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validar datos de entrada
            $data = $request->validate([
                'name' => 'required|string|max:150|unique:research_groups,name',
                'initials' => 'required|string|max:20|unique:research_groups,initials',
                'description' => 'required|string|min:10',
            ], [
                'name.required' => 'El nombre del grupo es obligatorio.',
                'name.max' => 'El nombre no puede superar los 150 caracteres.',
                'name.unique' => 'Ya existe un grupo de investigación con este nombre.',
                'initials.required' => 'Las iniciales del grupo son obligatorias.',
                'initials.max' => 'Las iniciales no pueden superar los 20 caracteres.',
                'initials.unique' => 'Ya existe un grupo con estas iniciales.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            ]);

            return DB::transaction(function () use ($data) {
                // Crear grupo de investigación
                $researchGroup = ResearchStaffResearchGroup::create($data);

                // Registrar evento en logs
                Log::info('Grupo de investigación creado', [
                    'research_group_id' => $researchGroup->id,
                    'research_group_name' => $researchGroup->name,
                    'initials' => $researchGroup->initials,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('research-groups.index')
                    ->with('success', "Grupo de investigación '{$researchGroup->name}' creado correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear grupo de investigación: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el grupo de investigación.')
                ->withInput();
        }
    }

    /**
     * Muestra el detalle de un grupo de investigación
     *
     * @param ResearchGroup $researchGroup Grupo a mostrar
     * @return View Vista con detalle
     */
    public function show(ResearchStaffResearchGroup $researchGroup): View
    {
        // Verificar si fue eliminado
        if ($researchGroup->trashed()) {
            abort(404, 'El grupo de investigación no está disponible.');
        }

        // Cargar relaciones ordenadas
        $researchGroup->load([
            'programs' => fn ($query) => $query->orderBy('name'),
            'investigationLines' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('research-groups.show', compact('researchGroup'));
    }

    /**
     * Muestra el formulario para editar un grupo de investigación
     *
     * @param ResearchGroup $researchGroup Grupo a editar
     * @return View Vista del formulario de edición
     */
    public function edit(ResearchStaffResearchGroup $researchGroup): View
    {
        // Verificar si fue eliminado
        if ($researchGroup->trashed()) {
            abort(404, 'El grupo de investigación no está disponible.');
        }

        return view('research-groups.edit', compact('researchGroup'));
    }

    /**
     * Actualiza un grupo de investigación existente
     *
     * @param Request $request Datos del formulario
     * @param ResearchGroup $researchGroup Grupo a actualizar
     * @return RedirectResponse Redirección con mensaje
     */
    public function update(Request $request, ResearchStaffResearchGroup $researchGroup): RedirectResponse
    {
        try {
            // Validar datos excluyendo el grupo actual en unicidad
            $data = $request->validate([
                'name' => 'required|string|max:150|unique:research_groups,name,' . $researchGroup->id,
                'initials' => 'required|string|max:20|unique:research_groups,initials,' . $researchGroup->id,
                'description' => 'required|string|min:10',
            ], [
                'name.required' => 'El nombre del grupo es obligatorio.',
                'name.max' => 'El nombre no puede superar los 150 caracteres.',
                'name.unique' => 'Ya existe otro grupo de investigación con este nombre.',
                'initials.required' => 'Las iniciales del grupo son obligatorias.',
                'initials.max' => 'Las iniciales no pueden superar los 20 caracteres.',
                'initials.unique' => 'Ya existe otro grupo con estas iniciales.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            ]);

            return DB::transaction(function () use ($researchGroup, $data) {
                // Verificar si fue eliminado
                if ($researchGroup->trashed()) {
                    return redirect()->route('research-groups.index')
                        ->with('error', 'No se puede actualizar un grupo de investigación eliminado.');
                }

                // Actualizar
                $researchGroup->update($data);

                // Registrar evento en logs
                Log::info('Grupo de investigación actualizado', [
                    'research_group_id' => $researchGroup->id,
                    'research_group_name' => $researchGroup->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('research-groups.index')
                    ->with('success', "Grupo de investigación '{$researchGroup->name}' actualizado correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar grupo de investigación: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el grupo de investigación.')
                ->withInput();
        }
    }

    /**
     * Elimina lógicamente (soft delete) un grupo de investigación
     *
     * Verifica que no tenga programas o líneas de investigación asociadas.
     *
     * @param ResearchGroup $researchGroup Grupo a eliminar
     * @return RedirectResponse Redirección con mensaje
     */
    public function destroy(ResearchStaffResearchGroup $researchGroup): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($researchGroup) {
                // Verificar si ya fue eliminado
                if ($researchGroup->trashed()) {
                    return redirect()->route('research-groups.index')
                        ->with('error', 'El grupo de investigación ya fue eliminado.');
                }

                // Verificar si tiene programas o líneas de investigación asociadas
                if ($researchGroup->programs()->exists() || $researchGroup->investigationLines()->exists()) {
                    return redirect()->route('research-groups.index')
                        ->with('error', 'No se puede eliminar el grupo porque tiene programas o líneas de investigación asociadas.');
                }

                $name = $researchGroup->name;

                // Realizar soft delete
                $researchGroup->delete();

                // Registrar evento en logs
                Log::info('Grupo de investigación eliminado', [
                    'research_group_id' => $researchGroup->id,
                    'research_group_name' => $name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('research-groups.index')
                    ->with('success', "Grupo de investigación '{$name}' eliminado correctamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar grupo de investigación: ' . $e->getMessage());

            return redirect()->route('research-groups.index')
                ->with('error', 'Ocurrió un error al eliminar el grupo de investigación.');
        }
    }

    /**
     * Restaura un grupo de investigación eliminado
     *
     * @param int $id ID del grupo a restaurar
     * @return RedirectResponse Redirección con mensaje
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar grupo incluyendo eliminados
                $researchGroup = ResearchStaffResearchGroup::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$researchGroup->trashed()) {
                    return redirect()->route('research-groups.index')
                        ->with('error', 'El grupo de investigación no está eliminado.');
                }

                // Restaurar
                $researchGroup->restore();

                // Registrar evento en logs
                Log::info('Grupo de investigación restaurado', [
                    'research_group_id' => $researchGroup->id,
                    'research_group_name' => $researchGroup->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('research-groups.index')
                    ->with('success', "Grupo de investigación '{$researchGroup->name}' restaurado correctamente.");
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('research-groups.index')
                ->with('error', 'No se encontró el grupo de investigación especificado.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar grupo de investigación: ' . $e->getMessage());

            return redirect()->route('research-groups.index')
                ->with('error', 'Ocurrió un error al restaurar el grupo de investigación.');
        }
    }
}
