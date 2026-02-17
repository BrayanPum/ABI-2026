<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the content_framework_project pivot table.
 *
 * Table created:
 * - content_framework_project: Establishes a many-to-many relationship between content frameworks and projects.
 *
 * Key details:
 * - References the 'content_frameworks' table through 'content_framework_id' (cascade on update and delete).
 * - References the 'projects' table through 'project_id' (cascade on update and delete).
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
        Schema::create('content_framework_project', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_framework_id')
                ->constrained('content_frameworks')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('project_id')
                ->constrained('projects')
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
        Schema::dropIfExists('content_framework_project');
    }
};
