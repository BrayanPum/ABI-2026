<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the professor_project pivot table.
 *
 * Table created:
 * - professor_project: Establishes a many-to-many relationship between professors and projects.
 *
 * Key details:
 * - References the 'professors' table through 'professor_id' (cascade on update and delete).
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
        Schema::create('professor_project', function (Blueprint $table) {
            $table->id();

            $table->foreignId('professor_id')
                ->constrained('professors')
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
        Schema::dropIfExists('professor_project');
    }
};
