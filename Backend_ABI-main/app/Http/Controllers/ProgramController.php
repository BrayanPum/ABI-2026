<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffProgram;
use App\Models\ResearchStaff\ResearchStaffResearchGroup;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de programas académicos
 *
 * Maneja el CRUD completo de programas con soft delete,
 * validaciones de código único y logging de operaciones.
 */
class ProgramController extends Controller
{
    /**
     * Muestra el listado de programas con filtros
     *
     * Permite filtrar por búsqueda de texto (nombre, código)
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

            // Query base con relaciones
            $programs = ResearchStaffProgram::query()
                ->with('researchGroup')
                ->when($search, function ($query, string $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
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

            return view('programs.index', [
                'programs' => $programs,
                'researchGroups' => $researchGroups,
                'search' => $search,
                'researchGroupId' => $researchGroupId,
                'perPage' => $perPage,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar programas: ' . $e->getMessage());

            return view('programs.index', [
                'programs' => collect(),
                'researchGroups' => collect(),
                'search' => '',
                'researchGroupId' => null,
                'perPage' => 10,
                'error' => 'Ocurrió un error al cargar los programas.',
            ]);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo programa
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        return view('programs.create', [
            'program' => new ResearchStaffProgram(),
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Almacena un nuevo programa en la base de datos
     *
     * @param Request $request Datos del formulario
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validar datos de entrada
            $data = $request->validate([
                'code' => 'required|integer|min:1|unique:programs,code',
                'name' => 'required|string|max:100',
                'research_group_id' => 'required|exists:research_groups,id',
            ], [
                'code.required' => 'El código del programa es obligatorio.',
                'code.integer' => 'El código debe ser un número entero.',
                'code.min' => 'El código debe ser mayor a 0.',
                'code.unique' => 'Ya existe un programa con este código.',
                'name.required' => 'El nombre del programa es obligatorio.',
                'name.max' => 'El nombre no puede superar los 100 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigación.',
                'research_group_id.exists' => 'El grupo de investigación seleccionado no es válido.',
            ]);

            return DB::transaction(function () use ($data) {
                // Crear programa
                $program = ResearchStaffProgram::create($data);

                // Registrar evento en logs
                Log::info('Programa creado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'research_group_id' => $program->research_group_id,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$program->name}' creado correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear programa: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el programa.')
                ->withInput();
        }
    }

    /**
     * Muestra el detalle de un programa
     *
     * @param Program $program Programa a mostrar
     * @return View Vista con detalle
     */
    public function show(ResearchStaffProgram $program): View
    {
        // Verificar si fue eliminado
        if ($program->trashed()) {
            abort(404, 'El programa no está disponible.');
        }

        // Cargar relaciones
        $program->load('researchGroup');

        return view('programs.show', compact('program'));
    }

    /**
     * Muestra el formulario para editar un programa
     *
     * @param Program $program Programa a editar
     * @return View Vista del formulario de edición
     */
    public function edit(ResearchStaffProgram $program): View
    {
        // Verificar si fue eliminado
        if ($program->trashed()) {
            abort(404, 'El programa no está disponible.');
        }

        return view('programs.edit', [
            'program' => $program,
            'researchGroups' => ResearchStaffResearchGroup::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Actualiza un programa existente en la base de datos
     *
     * @param Request $request Datos del formulario
     * @param Program $program Programa a actualizar
     * @return RedirectResponse Redirección con mensaje
     */
    public function update(Request $request, ResearchStaffProgram $program): RedirectResponse
    {
        try {
            // Validar datos excluyendo el programa actual en unicidad
            $data = $request->validate([
                'code' => 'required|integer|min:1|unique:programs,code,' . $program->id,
                'name' => 'required|string|max:100',
                'research_group_id' => 'required|exists:research_groups,id',
            ], [
                'code.required' => 'El código del programa es obligatorio.',
                'code.integer' => 'El código debe ser un número entero.',
                'code.min' => 'El código debe ser mayor a 0.',
                'code.unique' => 'Ya existe otro programa con este código.',
                'name.required' => 'El nombre del programa es obligatorio.',
                'name.max' => 'El nombre no puede superar los 100 caracteres.',
                'research_group_id.required' => 'Debe seleccionar un grupo de investigación.',
                'research_group_id.exists' => 'El grupo de investigación seleccionado no es válido.',
            ]);

            return DB::transaction(function () use ($program, $data) {
                // Verificar si fue eliminado
                if ($program->trashed()) {
                    return redirect()->route('programs.index')
                        ->with('error', 'No se puede actualizar un programa eliminado.');
                }

                // Actualizar
                $program->update($data);

                // Registrar evento en logs
                Log::info('Programa actualizado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$program->name}' actualizado correctamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar programa: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el programa.')
                ->withInput();
        }
    }

    /**
     * Elimina lógicamente (soft delete) un programa
     *
     * Verifica que no tenga información relacionada antes de eliminar.
     *
     * @param Program $program Programa a eliminar
     * @return RedirectResponse Redirección con mensaje
     */
    public function destroy(ResearchStaffProgram $program): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($program) {
                // Verificar si ya fue eliminado
                if ($program->trashed()) {
                    return redirect()->route('programs.index')
                        ->with('error', 'El programa ya fue eliminado.');
                }

                $name = $program->name;

                // Realizar soft delete
                $program->delete();

                // Registrar evento en logs
                Log::info('Programa eliminado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()
                    ->route('programs.index')
                    ->with('success', "Programa '{$name}' eliminado correctamente.");
            });

        } catch (QueryException $exception) {
            Log::error('Error de integridad al eliminar programa: ' . $exception->getMessage());

            return redirect()
                ->route('programs.index')
                ->with('error', 'No se puede eliminar el programa porque tiene información relacionada.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar programa: ' . $e->getMessage());

            return redirect()->route('programs.index')
                ->with('error', 'Ocurrió un error al eliminar el programa.');
        }
    }

    /**
     * Restaura un programa eliminado
     *
     * @param int $id ID del programa a restaurar
     * @return RedirectResponse Redirección con mensaje
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar programa incluyendo eliminados
                $program = ResearchStaffProgram::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$program->trashed()) {
                    return redirect()->route('programs.index')
                        ->with('error', 'El programa no está eliminado.');
                }

                // Restaurar
                $program->restore();

                // Registrar evento en logs
                Log::info('Programa restaurado', [
                    'program_id' => $program->id,
                    'program_code' => $program->code,
                    'program_name' => $program->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('programs.index')
                    ->with('success', "Programa '{$program->name}' restaurado correctamente.");
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('programs.index')
                ->with('error', 'No se encontró el programa especificado.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar programa: ' . $e->getMessage());

            return redirect()->route('programs.index')
                ->with('error', 'Ocurrió un error al restaurar el programa.');
        }
    }
}
