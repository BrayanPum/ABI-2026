<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentVersionRequest;
use App\Models\ResearchStaff\ResearchStaffContentVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de versiones de contenido
 *
 * Maneja la relación entre contenidos y versiones de proyectos,
 * permitiendo el registro y actualización de valores diligenciados
 * con soft delete y logging de operaciones.
 */
class ContentVersionController extends Controller
{
    /**
     * Lista los valores diligenciados por contenido y versión
     *
     * Permite filtrar por version_id, content_id, project_id y búsqueda de texto.
     *
     * @param Request $request Parámetros de consulta
     * @return JsonResponse Listado paginado de content_versions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validar y obtener parámetro de paginación
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            // Construir query base con relaciones
            $query = ResearchStaffContentVersion::query()->with(['content', 'version.project']);

            // Aplicar filtros si existen
            if ($versionId = $request->query('version_id')) {
                $query->where('version_id', $versionId);
            }

            if ($contentId = $request->query('content_id')) {
                $query->where('content_id', $contentId);
            }

            if ($projectId = $request->query('project_id')) {
                $query->whereHas('version', function ($q) use ($projectId) {
                    $q->where('project_id', $projectId);
                });
            }

            // Búsqueda por valor
            if ($search = trim((string) $request->query('search', ''))) {
                $query->where('value', 'like', '%' . $search . '%');
            }

            // Obtener resultados paginados
            $contentVersions = $query
                ->orderByDesc('updated_at')
                ->paginate($perPage)
                ->withQueryString();

            return response()->json($contentVersions);

        } catch (\Exception $e) {
            Log::error('Error al listar versiones de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener los registros.',
            ], 500);
        }
    }

    /**
     * Registra un nuevo valor de contenido para una versión
     *
     * @param ContentVersionRequest $request Datos validados del registro
     * @return JsonResponse Registro creado con código 201
     */
    public function store(ContentVersionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            return DB::transaction(function () use ($data) {
                // Crear registro de content_version
                $contentVersion = ResearchStaffContentVersion::create($data);

                // Registrar evento en logs
                Log::info('Versión de contenido creada', [
                    'content_version_id' => $contentVersion->id,
                    'content_id' => $contentVersion->content_id,
                    'version_id' => $contentVersion->version_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Contenido diligenciado correctamente.',
                    'data' => $contentVersion->load(['content', 'version.project']),
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('Error al crear versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al crear el registro.',
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un registro contenido-versión
     *
     * @param ContentVersion $contentVersion Registro a mostrar
     * @return JsonResponse Detalle del registro
     */
    public function show(ResearchStaffContentVersion $contentVersion): JsonResponse
    {
        try {
            // Verificar si fue eliminado
            if ($contentVersion->trashed()) {
                return response()->json([
                    'message' => 'El registro no está disponible.',
                ], 404);
            }

            // Cargar relaciones
            $contentVersion->load(['content', 'version.project']);

            return response()->json($contentVersion);

        } catch (\Exception $e) {
            Log::error('Error al mostrar versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener el registro.',
            ], 500);
        }
    }

    /**
     * Actualiza el valor de un contenido para una versión
     *
     * @param ContentVersionRequest $request Datos validados
     * @param ContentVersion $contentVersion Registro a actualizar
     * @return JsonResponse Registro actualizado
     */
    public function update(ContentVersionRequest $request, ResearchStaffContentVersion $contentVersion): JsonResponse
    {
        try {
            $data = $request->validated();

            return DB::transaction(function () use ($contentVersion, $data) {
                // Verificar si fue eliminado
                if ($contentVersion->trashed()) {
                    return response()->json([
                        'message' => 'No se puede actualizar un registro eliminado.',
                    ], 410);
                }

                // Actualizar registro
                $contentVersion->update($data);

                // Registrar evento en logs
                Log::info('Versión de contenido actualizada', [
                    'content_version_id' => $contentVersion->id,
                    'content_id' => $contentVersion->content_id,
                    'version_id' => $contentVersion->version_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Registro actualizado correctamente.',
                    'data' => $contentVersion->load(['content', 'version.project']),
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error al actualizar versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el registro.',
            ], 500);
        }
    }

    /**
     * Elimina lógicamente (soft delete) un registro contenido-versión
     *
     * @param ContentVersion $contentVersion Registro a eliminar
     * @return JsonResponse Respuesta sin contenido (204)
     */
    public function destroy(ResearchStaffContentVersion $contentVersion): JsonResponse
    {
        try {
            return DB::transaction(function () use ($contentVersion) {
                // Verificar si ya fue eliminado
                if ($contentVersion->trashed()) {
                    return response()->json([
                        'message' => 'El registro ya fue eliminado.',
                    ], 410);
                }

                // Realizar soft delete
                $contentVersion->delete();

                // Registrar evento en logs
                Log::info('Versión de contenido eliminada', [
                    'content_version_id' => $contentVersion->id,
                    'content_id' => $contentVersion->content_id,
                    'version_id' => $contentVersion->version_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json(null, 204);
            });

        } catch (\Exception $e) {
            Log::error('Error al eliminar versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al eliminar el registro.',
            ], 500);
        }
    }

    /**
     * Restaura un registro contenido-versión eliminado
     *
     * @param int $id ID del registro a restaurar
     * @return JsonResponse Registro restaurado
     */
    public function restore(int $id): JsonResponse
    {
        try {
            return DB::transaction(function () use ($id) {
                // Buscar registro incluyendo eliminados
                $contentVersion = ResearchStaffContentVersion::withTrashed()->findOrFail($id);

                // Verificar si está eliminado
                if (!$contentVersion->trashed()) {
                    return response()->json([
                        'message' => 'El registro no está eliminado.',
                    ], 400);
                }

                // Restaurar
                $contentVersion->restore();

                // Registrar evento en logs
                Log::info('Versión de contenido restaurada', [
                    'content_version_id' => $contentVersion->id,
                    'content_id' => $contentVersion->content_id,
                    'version_id' => $contentVersion->version_id,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'message' => 'Registro restaurado correctamente.',
                    'data' => $contentVersion->load(['content', 'version.project']),
                ]);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No se encontró el registro especificado.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al restaurar versión de contenido: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al restaurar el registro.',
            ], 500);
        }
    }
}
