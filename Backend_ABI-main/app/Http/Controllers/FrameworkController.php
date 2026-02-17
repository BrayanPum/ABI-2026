<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffFramework;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Controlador para la gestión de frameworks
 *
 * Maneja el CRUD completo de frameworks con soft delete,
 * validaciones robustas, búsqueda avanzada y logging de operaciones.
 */
class FrameworkController extends Controller
{
    /**
     * Muestra el listado de frameworks con opciones de filtrado
     *
     * Permite filtrar por búsqueda de texto (nombre, descripción, ID)
     * y por año (frameworks vigentes en ese año).
     *
     * @param Request $request Parámetros de consulta
     * @return View Vista con listado paginado de frameworks
     */
    public function index(Request $request): View
    {
        // Obtener parámetros de filtrado y búsqueda
        $search = $request->get('search');
        $year = $request->get('year');
        $perPageOptions = [10, 20, 30];
        $perPage = (int) $request->get('per_page', $perPageOptions[0]);

        // Validar que per_page esté en las opciones permitidas
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = $perPageOptions[0];
        }

        try {

            // Query base - solo frameworks no eliminados
            $query = ResearchStaffFramework::query();

            // Aplicar filtro de búsqueda
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');

                    // Si la búsqueda es numérica, también buscar por ID
                    if (is_numeric($search)) {
                        $q->orWhere('id', $search);
                    }
                });
            }

            // Aplicar filtro por año
            if ($year) {
                $query->where(function ($q) use ($year) {
                    $q->where('start_year', '<=', $year)
                        ->where(function ($subQ) use ($year) {
                            $subQ->whereNull('end_year')
                                ->orWhere('end_year', '>=', $year);
                        });
                });
            }

            // Ordenamiento por defecto
            $query->orderBy('start_year', 'desc')
                ->orderBy('name', 'asc');

            // Paginación
            $frameworks = $query->paginate($perPage);

            // Mantener parámetros en paginación
            $frameworks->appends($request->query());

            return view('framework.index', [
                'frameworks' => $frameworks,
                'search' => $search,
                'year' => $year,
                'perPage' => $perPage,
                'perPageOptions' => $perPageOptions,
                'i' => ($frameworks->currentPage() - 1) * $frameworks->perPage(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar frameworks: ' . $e->getMessage());

            $emptyPaginator = new LengthAwarePaginator(
                collect(),
                0,
                $perPage,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return view('framework.index', [
                'frameworks' => $emptyPaginator,
                'search' => $search,
                'year' => $year,
                'perPage' => $perPage,
                'perPageOptions' => $perPageOptions,
                'i' => 0,
                'error' => 'Ocurrió un error al cargar los marcos.',
            ]);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo framework
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        $framework = new ResearchStaffFramework();
        return view('framework.create', compact('framework'));
    }

    /**
     * Almacena un nuevo framework en la base de datos
     *
     * @param Request $request Datos del formulario
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validar datos de entrada
            $validatedData = $request->validate(ResearchStaffFramework::$rules, [
                'name.required' => 'El nombre del marco es obligatorio.',
                'name.unique' => 'Ya existe un marco con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'link.url' => 'El enlace debe ser una URL válida.',
                'link.max' => 'El enlace no puede superar los 200 caracteres.',
                'start_year.required' => 'El año de inicio es obligatorio.',
                'start_year.integer' => 'El año de inicio debe ser un número.',
                'start_year.min' => 'El año de inicio no puede ser menor a 1900.',
                'end_year.after_or_equal' => 'El año de fin debe ser posterior o igual al año de inicio.',
            ]);

            return DB::transaction(function () use ($validatedData) {
                // Asegurar que link no sea null
                $validatedData['link'] = $validatedData['link'] ?? '';

                // Crear framework
                $framework = ResearchStaffFramework::create($validatedData);

                // Registrar evento en logs
                Log::info('Framework creado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $framework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('frameworks.index')
                    ->with('success', "Framework '{$framework->name}' creado exitosamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al crear framework: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el framework. Por favor, intente nuevamente.')
                ->withInput();
        }
    }

    /**
     * Muestra el detalle de un framework específico
     *
     * @param Framework $framework Framework a mostrar
     * @return View Vista con detalle del framework
     */
    public function show(ResearchStaffFramework $framework): View
    {
        // Verificar si fue eliminado
        if ($framework->trashed()) {
            abort(404, 'El framework no está disponible.');
        }

        return view('framework.show', compact('framework'));
    }

    /**
     * Muestra el formulario para editar un framework
     *
     * @param Framework $framework Framework a editar
     * @return View Vista del formulario de edición
     */
    public function edit(ResearchStaffFramework $framework): View
    {
        // Verificar si fue eliminado
        if ($framework->trashed()) {
            abort(404, 'El framework no está disponible.');
        }

        return view('framework.edit', compact('framework'));
    }

    /**
     * Actualiza un framework existente en la base de datos
     *
     * @param Request $request Datos del formulario
     * @param Framework $framework Framework a actualizar
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function update(Request $request, ResearchStaffFramework $framework): RedirectResponse
    {
        try {
            // Reglas de validación excluyendo el framework actual en unicidad
            $rules = [
                'name' => 'required|unique:frameworks,name,' . $framework->id,
                'description' => 'required|min:10',
                'link' => 'nullable|url|max:200',
                'start_year' => 'required|integer|min:1900',
                'end_year' => 'nullable|integer|after_or_equal:start_year',
            ];

            // Validar datos
            $validatedData = $request->validate($rules, [
                'name.required' => 'El nombre del framework es obligatorio.',
                'name.unique' => 'Ya existe otro framework con este nombre.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min' => 'La descripción debe tener al menos 10 caracteres.',
                'link.url' => 'El enlace debe ser una URL válida.',
                'link.max' => 'El enlace no puede superar los 200 caracteres.',
                'start_year.required' => 'El año de inicio es obligatorio.',
                'start_year.integer' => 'El año de inicio debe ser un número.',
                'start_year.min' => 'El año de inicio no puede ser menor a 1900.',
                'end_year.after_or_equal' => 'El año de fin debe ser posterior o igual al año de inicio.',
            ]);

            return DB::transaction(function () use ($framework, $validatedData) {
                // Verificar si fue eliminado
                if ($framework->trashed()) {
                    return redirect()->route('frameworks.index')
                        ->with('error', 'No se puede actualizar un framework eliminado.');
                }

                // Asegurar que link no sea null
                $validatedData['link'] = $validatedData['link'] ?? '';

                // Actualizar framework
                $framework->update($validatedData);

                // Registrar evento en logs
                Log::info('Framework actualizado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $framework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('frameworks.index')
                    ->with('success', "Framework '{$framework->name}' actualizado exitosamente.");
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar framework: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el framework. Por favor, intente nuevamente.')
                ->withInput();
        }
    }

    /**
     * Elimina lógicamente (soft delete) un framework
     *
     * Verifica que no tenga contenidos asociados antes de eliminar.
     *
     * @param Framework $framework Framework a eliminar
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function destroy(ResearchStaffFramework $framework): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($framework) {
                // Verificar si ya fue eliminado
                if ($framework->trashed()) {
                    return redirect()->route('frameworks.index')
                        ->with('error', 'El framework ya fue eliminado.');
                }

                // Verificar si tiene contenidos asociados
                if ($framework->contentFrameworks()->exists()) {
                    return redirect()->route('frameworks.index')
                        ->with('error', 'No se puede eliminar el framework porque tiene contenidos asociados.');
                }

                $frameworkName = $framework->name;

                // Realizar soft delete
                $framework->delete();

                // Registrar evento en logs
                Log::info('Framework eliminado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $frameworkName,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('frameworks.index')
                    ->with('success', "Framework '{$frameworkName}' eliminado exitosamente.");
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar framework: ' . $e->getMessage());

            return redirect()->route('frameworks.index')
                ->with('error', 'Ocurrió un error al eliminar el framework. Por favor, intente nuevamente.');
        }
    }

    /**
     * Restaura un framework eliminado lógicamente
     *
     * @param int $id ID del framework a restaurar
     * @return RedirectResponse Redirección con mensaje de resultado
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar framework incluyendo eliminados
                $framework = ResearchStaffFramework::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$framework->trashed()) {
                    return redirect()->route('frameworks.index')
                        ->with('error', 'El framework no está eliminado.');
                }

                // Restaurar
                $framework->restore();

                // Registrar evento en logs
                Log::info('Framework restaurado', [
                    'framework_id' => $framework->id,
                    'framework_name' => $framework->name,
                    'user_id' => auth()->id(),
                ]);

                return redirect()->route('frameworks.index')
                    ->with('success', "Framework '{$framework->name}' restaurado correctamente.");
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('frameworks.index')
                ->with('error', 'No se encontró el framework especificado.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar framework: ' . $e->getMessage());

            return redirect()->route('frameworks.index')
                ->with('error', 'Ocurrió un error al restaurar el framework.');
        }
    }
}
