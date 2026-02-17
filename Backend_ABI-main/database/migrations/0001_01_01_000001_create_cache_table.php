<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create cache-related tables.
 *
 * This migration defines the database structure for Laravel's cache system:
 *
 * - cache: Stores cached key-value pairs with expiration time.
 * 
 * - cache_locks: Manages distributed locks for preventing race conditions.
 * 
 * Key details:
 * - Both tables use string-based keys as primary identifiers.
 * - These tables are only used if the database cache/lock driver is enabled.
 * - Provides reliable cache persistence and concurrency control in multi-instance setups.
 *
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
