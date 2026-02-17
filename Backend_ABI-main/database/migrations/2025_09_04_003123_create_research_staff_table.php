<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the research_staff table.
 *
 * Table created:
 * - research_staff: Stores research staff members linked to user accounts.
 *
 * Key details:
 * - 'card_id' is unique to avoid duplicates.
 * - References the 'users' table through 'user_id'.
 * - Uses cascading on update and delete to maintain referential integrity.
 *
 * Note: If additional modifications are needed for the 'research_staff' table, they should be made in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('research_staff', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 25)->unique();
            $table->string('name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 20);
            $table->foreignId('user_id')
                ->constrained('users')
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
        Schema::dropIfExists('research_staff');
    }
};
