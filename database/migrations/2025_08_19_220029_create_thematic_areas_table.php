<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the thematic_areas table.
 *
 * Table created:
 * - thematic_areas: Stores thematic areas linked to investigation lines.
 *
 * Key details:
 * - References the 'investigation_lines' table through 'investigation_line_id'.
 * - Uses cascading on update and delete to maintain referential integrity.
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
        Schema::create('thematic_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description');
            $table->foreignId('investigation_line_id')
                ->constrained('investigation_lines')
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
        Schema::dropIfExists('thematic_areas');
    }
};
