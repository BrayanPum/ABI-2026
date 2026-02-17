<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add 'semester' and 'user_id' columns to the students table.
 *
 * Table modified:
 * - students: Adds 'semester' to track student progression and 'user_id' as a foreign key to users.
 *
 * Key details:
 * - References the 'users' table through 'user_id'.
 * - Uses cascading on update and delete to maintain referential integrity.
 *
 * Note: If additional modifications are needed for the 'students' table, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->integer('semester')->after('phone');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['semester', 'user_id']);
        });
    }
};
