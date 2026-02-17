<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the investigation_lines table.
 *
 * Table created:
 * - investigation_lines: Stores research lines associated with research groups.
 *
 * Key details:
 * - References the 'research_groups' table through 'research_group_id'.
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
        Schema::create('investigation_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description');
            $table->foreignId('research_group_id')
                ->constrained('research_groups')
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
        Schema::dropIfExists('investigation_lines');
    }
};
