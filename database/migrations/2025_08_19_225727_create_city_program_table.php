<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the city_program pivot table.
 *
 * Table created:
 * - city_program: Defines the many-to-many relationship between cities and programs.
 *
 * Key details:
 * - References the 'cities' table through 'city_id' (cascade on update and delete).
 * - References the 'programs' table through 'program_id' (cascade on update and delete).
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
        Schema::create('city_program', function (Blueprint $table) {
            $table->id();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('program_id')
                ->constrained('programs')
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
        Schema::dropIfExists('city_program');
    }
};
