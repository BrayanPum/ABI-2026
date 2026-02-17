<?php

namespace App\Http\Controllers;

use App\Http\Requests\VersionRequest;
use App\Models\ResearchStaff\ResearchStaffContentVersion;
use App\Models\ResearchStaff\ResearchStaffVersion;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de versiones de proyectos
 *
 * Maneja el CRUD completo de versiones con soft delete,
 * validaciones y logging de operaciones. Las versiones representan
 * diferentes iteraciones o estados de un proyecto.
 */
class VersionController extends Controller
{
    /**
     * Lista las versiones registradas con filtros
     *
     * Permite filtrar por project_id y paginar resultados.
     *
     * @param Request $request Parámetros de consulta
     * @return JsonResponse Listado paginado de versiones
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validación de paginación
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            // Query base
            $query = ResearchStaffVersion::query()->with('project');

            /**
             * FILTRO: ID de proyecto
             */
            if ($projectId = $request->query('project_id')) {
                $query->where('project_id', $projectId);
            }

            /**
             * FILTRO: Título de proyecto
             */
            if ($title = $request->query('title')) {
                $query->whereHas('project', function ($q) use ($title) {
                    $q->where('title', 'like', "%{$title}%");
                });
            }

            /**
             * FILTRO: Programa (project → professors → cityProgram → program_id)
             */
            if ($programId = $request->query('program_id')) {
                $query->whereHas('project.professors.cityProgram', function ($q) use ($programId) {
                    $q->where('program_id', $programId);
                });
            }

            // Orden + Paginación
            $versions = $query
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($versions);

        } catch (\Exception $e) {
            Log::error('Error al listar versiones: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener las versiones.',
            ], 500);
        }
    }


    /**
     * Crea una nueva versión para un proyecto
     *
     * @param VersionRequest $request Datos validados de la versión
     * @return JsonResponse Versión creada con código 201
     */
    public function store(VersionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            return DB::transaction(function () use ($data) {
                // Crear versión
                $version = ResearchStaffVersion::create($data);

                // Registrar evento en logs
                Log::info('Versión creada', [
                    'version_id' => $version->id,
                    'project_id' => $version->project_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Versión creada correctamente.',
                    'data' => $version->load('project'),
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('Error al crear versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al crear la versión.',
            ], 500);
        }
    }

    /**
     * Muestra una versión específica con sus contenidos
     *
     * @param Version $version Versión a mostrar
     * @return JsonResponse Detalle de la versión
     */
    public function show(ResearchStaffVersion $version): JsonResponse
    {
        try {
            // Verificar si fue eliminada
            if ($version->trashed()) {
                return response()->json([
                    'message' => 'La versión no está disponible.',
                ], 404);
            }

            // Load the related project so the frontend can display contextual info.
            $version->load('project');

            // Gather associated contents using the model bound to the research staff connection.
            $contents = ResearchStaffContentVersion::query()
                ->with('content')
                ->where('version_id', $version->id)
                ->get()
                ->sortBy(function (ResearchStaffContentVersion $contentVersion) {
                    return Str::lower($contentVersion->content?->name ?? '');
                })
                ->values()
                ->map(function (ResearchStaffContentVersion $contentVersion) {
                    return [
                        'id' => $contentVersion->content?->id,
                        'name' => $contentVersion->content?->name,
                        'description' => $contentVersion->content?->description,
                        'pivot' => [
                            'id' => $contentVersion->id,
                            'content_id' => $contentVersion->content_id,
                            'version_id' => $contentVersion->version_id,
                            'value' => $contentVersion->value,
                            'created_at' => $contentVersion->created_at?->toJSON(),
                            'updated_at' => $contentVersion->updated_at?->toJSON(),
                        ],
                    ];
                })
                ->toArray();

            $payload = $version->toArray();
            $payload['contents'] = $contents;

            return response()->json($payload);

        } catch (\Exception $e) {
            Log::error('Error al mostrar versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener la versión.',
            ], 500);
        }
    }

    /**
     * Actualiza los datos de una versión
     *
     * @param VersionRequest $request Datos validados
     * @param Version $version Versión a actualizar
     * @return JsonResponse Versión actualizada
     */
    public function update(VersionRequest $request, ResearchStaffVersion $version): JsonResponse
    {
        try {
            $data = $request->validated();

            return DB::transaction(function () use ($version, $data) {
                // Verificar si fue eliminada
                if ($version->trashed()) {
                    return response()->json([
                        'message' => 'No se puede actualizar una versión eliminada.',
                    ], 410);
                }

                // Actualizar versión
                $version->update($data);

                // Registrar evento en logs
                Log::info('Versión actualizada', [
                    'version_id' => $version->id,
                    'project_id' => $version->project_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Versión actualizada correctamente.',
                    'data' => $version->load('project'),
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error al actualizar versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al actualizar la versión.',
            ], 500);
        }
    }

    /**
     * Elimina lógicamente (soft delete) una versión
     *
     * Verifica que no tenga contenidos diligenciados antes de eliminar.
     *
     * @param Version $version Versión a eliminar
     * @return JsonResponse Respuesta sin contenido (204) o error
     */
    public function destroy(ResearchStaffVersion $version): JsonResponse
    {
        try {
            return DB::transaction(function () use ($version) {
                // Verificar si ya fue eliminada
                if ($version->trashed()) {
                    return response()->json([
                        'message' => 'La versión ya fue eliminada.',
                    ], 410);
                }

                // Verificar si tiene contenidos diligenciados
                if ($version->contentVersions()->exists()) {
                    return response()->json([
                        'message' => 'No es posible eliminar la versión porque tiene contenidos diligenciados.',
                    ], 409);
                }

                // Realizar soft delete
                $version->delete();

                // Registrar evento en logs
                Log::info('Versión eliminada', [
                    'version_id' => $version->id,
                    'project_id' => $version->project_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json(null, 204);
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al eliminar la versión.',
            ], 500);
        }
    }

    /**
     * Restaura una versión eliminada
     *
     * @param int $id ID de la versión a restaurar
     * @return JsonResponse Versión restaurada
     */
    public function restore(int $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar versión incluyendo eliminadas
                $version = ResearchStaffVersion::withTrashed()->findOrFail($id);

                // Verificar si está eliminada
                if (!$version->trashed()) {
                    return response()->json([
                        'message' => 'La versión no está eliminada.',
                    ], 400);
                }

                // Restaurar
                $version->restore();

                // Registrar evento en logs
                Log::info('Versión restaurada', [
                    'version_id' => $version->id,
                    'project_id' => $version->project_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Versión restaurada correctamente.',
                    'data' => $version->load('project'),
                ]);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró la versión especificada.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar versión: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al restaurar la versión.',
            ], 500);
        }
    }
}
