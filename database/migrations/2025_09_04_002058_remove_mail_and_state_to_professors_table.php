<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to drop the 'mail' and 'state' columns from the professors table.
 *
 * Table modified:
 * - professors: Removes email ('mail') and active status ('state') columns.
 *
 * Note: If additional modifications are needed for the 'professors' table, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('professors', function (Blueprint $table) {
            $table->dropColumn(['mail', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professors', function (Blueprint $table) {
            $table->string('mail', 50);
            $table->boolean('state');
        });
    }
};
