<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to remove 'mail' and 'state' columns from the students table.
 *
 * Table modified:
 * - students: Removes unnecessary columns related to email and state.
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
            $table->dropColumn(['mail', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('mail', 50);
            $table->boolean('state');
        });
    }
};
