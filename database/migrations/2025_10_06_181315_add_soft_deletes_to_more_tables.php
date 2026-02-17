<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/**
 * Migration to add soft deletes to missing tables
 *
 * Affected tables:
 * - thematic_areas
 * - projects
 * - cities
 * - departments
 * - project_statuses
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('thematic_areas', 'deleted_at')) {
            Schema::table('thematic_areas', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('projects', 'deleted_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('cities', 'deleted_at')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('departments', 'deleted_at')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (!Schema::hasColumn('project_statuses', 'deleted_at')) {
            Schema::table('project_statuses', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('thematic_areas', 'deleted_at')) {
            Schema::table('thematic_areas', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('projects', 'deleted_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('cities', 'deleted_at')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('departments', 'deleted_at')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('project_statuses', 'deleted_at')) {
            Schema::table('project_statuses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
