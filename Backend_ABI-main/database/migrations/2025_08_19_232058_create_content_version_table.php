<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the content_version pivot table.
 *
 * Table created:
 * - content_version: Establishes a many-to-many relationship between contents and versions, storing additional value data.
 *
 * Key details:
 * - References the 'contents' table through 'content_id' (cascade on update and delete).
 * - References the 'versions' table through 'version_id' (cascade on update and delete).
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
        Schema::create('content_version', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_id')
                ->constrained('contents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreignId('version_id')
                ->constrained('versions')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->text('value');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_version');
    }
};
