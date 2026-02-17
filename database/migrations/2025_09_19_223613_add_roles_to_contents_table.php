<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add the 'roles' column to the contents table.
 *
 * Key details:
 * - Adds 'roles', a JSON column to store allowed roles for a content item.
 *   This allows flexible storage of multiple roles in a single field.
 *
 * Note: If further modifications to the 'contents' table are required, they should be implemented in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Only these roles are accepted: ['research_staff', 'professor', 'student', 'committee_leader']
            $table->json('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
