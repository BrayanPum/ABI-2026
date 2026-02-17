<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para agregar soft deletes a tablas de catálogo
 *
 * Agrega la columna deleted_at a todas las tablas que manejan
 * datos de catálogo, permitiendo eliminación lógica en lugar de física.
 *
 * Tablas afectadas:
 * - contents: Contenidos del catálogo
 * - content_frameworks: Frameworks de contenido
 * - content_version: Versiones de contenido
 * - frameworks: Marcos de trabajo
 * - investigation_lines: Líneas de investigación
 * - programs: Programas académicos
 * - research_groups: Grupos de investigación
 * - versions: Versiones de proyectos
 */
return new class extends Migration
{
    /**
     * Ejecuta las migraciones para agregar soft deletes
     *
     * @return void
     */
    public function up(): void
    {
        // Agregar deleted_at a la tabla contents
        if (!Schema::hasColumn('contents', 'deleted_at')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla content_frameworks
        if (!Schema::hasColumn('content_frameworks', 'deleted_at')) {
            Schema::table('content_frameworks', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla content_version
        if (!Schema::hasColumn('content_version', 'deleted_at')) {
            Schema::table('content_version', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla frameworks
        if (!Schema::hasColumn('frameworks', 'deleted_at')) {
            Schema::table('frameworks', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla investigation_lines
        if (!Schema::hasColumn('investigation_lines', 'deleted_at')) {
            Schema::table('investigation_lines', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla programs
        if (!Schema::hasColumn('programs', 'deleted_at')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla research_groups
        if (!Schema::hasColumn('research_groups', 'deleted_at')) {
            Schema::table('research_groups', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar deleted_at a la tabla versions
        if (!Schema::hasColumn('versions', 'deleted_at')) {
            Schema::table('versions', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    /**
     * Revierte las migraciones eliminando las columnas deleted_at
     *
     * @return void
     */
    public function down(): void
    {
        // Eliminar deleted_at de contents
        if (Schema::hasColumn('contents', 'deleted_at')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de content_frameworks
        if (Schema::hasColumn('content_frameworks', 'deleted_at')) {
            Schema::table('content_frameworks', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de content_version
        if (Schema::hasColumn('content_version', 'deleted_at')) {
            Schema::table('content_version', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de frameworks
        if (Schema::hasColumn('frameworks', 'deleted_at')) {
            Schema::table('frameworks', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de investigation_lines
        if (Schema::hasColumn('investigation_lines', 'deleted_at')) {
            Schema::table('investigation_lines', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de programs
        if (Schema::hasColumn('programs', 'deleted_at')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de research_groups
        if (Schema::hasColumn('research_groups', 'deleted_at')) {
            Schema::table('research_groups', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        // Eliminar deleted_at de versions
        if (Schema::hasColumn('versions', 'deleted_at')) {
            Schema::table('versions', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
