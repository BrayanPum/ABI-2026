<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add the 'initials' column to the research_groups table.
 *
 * Note: If further modifications to the 'research_groups' table are required, they should be implemented in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('research_groups', function (Blueprint $table) {
            $table->string('initials', 15)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_groups', function (Blueprint $table) {
            $table->dropColumn('initials');
        });
    }
};
