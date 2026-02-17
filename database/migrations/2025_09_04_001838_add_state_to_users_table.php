<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add the 'state' column to the users table.
 *
 * Table modified:
 * - users: Adds a boolean 'state' column to represent active/inactive status.
 *
 * Key details:
 * - 'state' column is boolean with a default value of true.
 *
 * Note: If additional modifications are needed for the 'users' table, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('state')->default(true)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('state');
        });
    }
};
