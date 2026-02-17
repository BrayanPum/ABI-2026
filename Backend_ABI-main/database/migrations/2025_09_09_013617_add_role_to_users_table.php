<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add the 'role' column to the users table.
 *
 * Key details:
 * - Adds 'role', an ENUM column.
 * - Default value is 'user'.
 *
 * Note: If further modifications to the 'users' table are required, they should be implemented in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['research_staff', 'professor', 'student', 'user', 'committee_leader'])->default('user')->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
