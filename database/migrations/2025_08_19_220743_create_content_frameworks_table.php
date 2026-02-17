<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the content_frameworks table.
 *
 * Table created:
 * - content_frameworks: Stores specific contents associated with a framework.
 *
 * Key details:
 * - References the 'frameworks' table through 'framework_id'.
 * - Uses cascading on update and delete to maintain referential integrity.
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
        Schema::create('content_frameworks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->text('description');
            $table->foreignId('framework_id')
                ->constrained('frameworks')
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
        Schema::dropIfExists('content_frameworks');
    }
};
