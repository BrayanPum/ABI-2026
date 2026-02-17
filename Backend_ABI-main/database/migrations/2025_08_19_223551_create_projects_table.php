<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the projects table.
 *
 * Table created:
 * - projects: Stores degree projects.
 *
 * Key details:
 * - References the 'thematic_areas' table through 'thematic_area_id' (cascade on update and delete).
 * - References the 'project_statuses' table through 'project_status_id' (cascade on update and delete).
 * - The 'evaluation_criteria' field is optional, since it is only provided by users with role 'professor'.
 *
 * Note: If changes or additions are required, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('evaluation_criteria')
                ->nullable()
                ->default(null);
            $table->foreignId('thematic_area_id')
                ->constrained('thematic_areas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('project_status_id')
                ->constrained('project_statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
