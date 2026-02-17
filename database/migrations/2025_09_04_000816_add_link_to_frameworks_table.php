<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add the 'link' column to the frameworks table.
 *
 * Table modified:
 * - frameworks: Adds an optional 'link' column to store related URLs.
 *
 * Note: If additional modifications are needed for the 'frameworks' table, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('frameworks', function (Blueprint $table) {
            $table->string('link', 200)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frameworks', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
