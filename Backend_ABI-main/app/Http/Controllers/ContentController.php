<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\Models\ResearchStaff\ResearchStaffContent;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la gestión de contenidos del catálogo
 *
 * Este controlador maneja el CRUD completo de contenidos, incluyendo:
 * - Listado paginado con filtros de búsqueda y roles
 * - Creación de nuevos contenidos
 * - Visualización de detalles con versiones asociadas
 * - Actualización de contenidos existentes
 * - Eliminación lógica (soft delete) con validaciones
 */
class ContentController extends Controller
{
    /**
     * Obtiene un listado paginado de contenidos disponibles
     *
     * Soporta filtrado por:
     * - Búsqueda general (nombre y descripción)
     * - Roles específicos (mediante campo JSON)
     * - Paginación configurable
     *
     * @param Request $request Petición HTTP con parámetros de filtro
     * @return JsonResponse Respuesta JSON con contenidos paginados
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'roles' => 'nullable', // Puede ser string o array
            ]);

            // Configurar paginación con valor por defecto y límite máximo
            $perPage = (int) ($validated['per_page'] ?? 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            // Iniciar consulta base
            $query = ResearchStaffContent::query();

            // Aplicar filtro de búsqueda si existe
            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');

                    // Si el término de búsqueda es numérico, buscar también por ID
                    if (is_numeric($search)) {
                        $q->orWhere('id', (int)$search);
                    }
                });
            }

            // Aplicar filtro de roles si existe
            $rolesFilter = $request->query('roles');
            if ($rolesFilter !== null && $rolesFilter !== '') {
                // Normalizar el filtro de roles a array
                if (is_string($rolesFilter)) {
                    $rolesFilter = json_decode($rolesFilter, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($rolesFilter)) {
                        $rolesFilter = array_filter(array_map('trim', explode(',', $rolesFilter)));
                    }
                }

                if (!is_array($rolesFilter)) {
                    $rolesFilter = [$rolesFilter];
                }

                // Aplicar filtro JSON para cada rol especificado
                foreach ($rolesFilter as $role) {
                    $role = trim((string) $role);
                    if ($role !== '') {
                        $query->whereJsonContains('roles', $role);
                    }
                }
            }

            // Obtener resultados paginados ordenados alfabéticamente
            $contents = $query
                ->orderBy('name')
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($contents);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos de entrada no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al listar contenidos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener los contenidos.',
            ], 500);
        }
    }

    /**
     * Crea un nuevo contenido en el catálogo
     *
     * Valida y registra un nuevo contenido dentro de una transacción
     * para garantizar integridad de datos.
     *
     * @param ContentRequest $request Petición validada con datos del contenido
     * @return JsonResponse Respuesta JSON con el contenido creado
     */
    public function store(ContentRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                // Crear el nuevo contenido
                $content = ResearchStaffContent::create($data);

                // Registrar evento en logs
                Log::info('Contenido creado exitosamente', [
                    'content_id' => $content->id,
                    'content_name' => $content->name,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Contenido creado correctamente.',
                    'data' => $content,
                ], 201);
            });

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear contenido: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al crear el contenido. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Muestra el detalle completo de un contenido específico
     *
     * Incluye todas las versiones asociadas ordenadas por fecha de creación
     *
     * @param Content $content Instancia del contenido a mostrar
     * @return JsonResponse Respuesta JSON con el contenido y sus versiones
     */
    public function show(ResearchStaffContent $content): JsonResponse
    {
        try {
            // Verificar si el contenido fue eliminado (soft delete)
            if ($content->trashed()) {
                return response()->json([
                    'message' => 'El contenido solicitado no está disponible.',
                ], 404);
            }

            // Attempt to load the related versions but fail gracefully if the connection does not expose them
            try {
                $content->load(['versions' => function ($query) {
                    $query->orderByDesc('content_version.created_at');
                }]);
            } catch (QueryException $exception) {
                Log::warning('Unable to load versions for the requested content. Returning base content data instead.', [
                    'content_id' => $content->id,
                    'error' => $exception->getMessage(),
                ]);
            }

            $response = [
                'id' => $content->id,
                'name' => $content->name,
                'description' => $content->description,
                'roles' => Arr::wrap($content->roles),
                'created_at' => $content->created_at,
                'updated_at' => $content->updated_at,
            ];

            if ($content->relationLoaded('versions')) {
                $response['versions'] = $content->versions->map(static function ($version) {
                    return [
                        'id' => $version->id,
                        'name' => $version->name,
                        'pivot' => [
                            'id' => $version->pivot->id ?? null,
                            'value' => $version->pivot->value ?? null,
                            'created_at' => $version->pivot->created_at ?? null,
                            'updated_at' => $version->pivot->updated_at ?? null,
                        ],
                    ];
                });
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error al mostrar contenido: ' . $e->getMessage(), [
                'content_id' => $content->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al obtener el contenido.',
            ], 500);
        }
    }

    /**
     * Actualiza los datos de un contenido existente
     *
     * Valida y actualiza el contenido dentro de una transacción
     *
     * @param ContentRequest $request Petición validada con datos actualizados
     * @param Content $content Instancia del contenido a actualizar
     * @return JsonResponse Respuesta JSON con el contenido actualizado
     */
    public function update(ContentRequest $request, ResearchStaffContent $content): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $content) {
                $data = $request->validated();

                // Verificar si el contenido fue eliminado
                if ($content->trashed()) {
                    return response()->json([
                        'message' => 'No se puede actualizar un contenido eliminado.',
                    ], 403);
                }

                // Actualizar el contenido
                $content->update($data);

                // Registrar evento en logs
                Log::info('Contenido actualizado exitosamente', [
                    'content_id' => $content->id,
                    'content_name' => $content->name,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Contenido actualizado correctamente.',
                    'data' => $content->fresh(),
                ]);
            });

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar contenido: ' . $e->getMessage(), [
                'content_id' => $content->id,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el contenido. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Elimina lógicamente (soft delete) un contenido del catálogo
     *
     * Verifica que no tenga versiones asociadas antes de eliminar.
     * Utiliza soft delete para mantener el registro en la base de datos.
     *
     * @param Content $content Instancia del contenido a eliminar
     * @return JsonResponse Respuesta JSON confirmando la eliminación
     */
    public function destroy(ResearchStaffContent $content): JsonResponse
    {
        try {
            return DB::transaction(function () use ($content) {
                // Verificar si ya fue eliminado
                if ($content->trashed()) {
                    return response()->json([
                        'message' => 'El contenido ya fue eliminado previamente.',
                    ], 410);
                }

                // Validar que no tenga versiones asociadas
                if ($content->contentVersions()->exists()) {
                    return response()->json([
                        'message' => 'No es posible eliminar el contenido porque tiene versiones asociadas. Elimine primero las versiones relacionadas.',
                    ], 409);
                }

                // Realizar soft delete
                $content->delete();

                // Registrar evento en logs
                Log::info('Contenido eliminado exitosamente', [
                    'content_id' => $content->id,
                    'content_name' => $content->name,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Contenido eliminado correctamente.',
                ], 200);
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar contenido: ' . $e->getMessage(), [
                'content_id' => $content->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al eliminar el contenido. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Restaura un contenido eliminado lógicamente
     *
     * Permite recuperar contenidos que fueron eliminados con soft delete
     *
     * @param int $id ID del contenido a restaurar
     * @return JsonResponse Respuesta JSON confirmando la restauración
     */
    public function restore(int $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar contenido incluyendo eliminados
                $content = ResearchStaffContent::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$content->trashed()) {
                    return response()->json([
                        'message' => 'El contenido no está eliminado.',
                    ], 400);
                }

                // Restaurar el contenido
                $content->restore();

                // Registrar evento en logs
                Log::info('Contenido restaurado exitosamente', [
                    'content_id' => $content->id,
                    'content_name' => $content->name,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Contenido restaurado correctamente.',
                    'data' => $content,
                ]);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el contenido especificado.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar contenido: ' . $e->getMessage(), [
                'content_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al restaurar el contenido. Por favor, intente nuevamente.',
            ], 500);
        }
    }
}
