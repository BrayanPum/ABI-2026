<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create authentication-related tables.
 *
 * This migration defines the base structure for user management in the application:
 *
 * - users: Stores registered users with basic authentication fields.
 *   - Includes email verification timestamp and remember token.
 *
 * - password_reset_tokens: Handles password recovery by storing reset tokens.
 *   - Uses email as the primary key.
 *
 * - sessions: Persists user sessions for authentication.
 *   - Includes IP address, user agent, payload and last activity timestamp.
 *
 * Key details:
 * - All tables follow Laravel's default authentication scaffolding.
 * - The 'users' table uses unique email constraint to avoid duplicates.
 * - The 'sessions' table links to users via foreign key (user_id).
 *
 * Note: If authentication requirements change (e.g. new roles or fields),
 * the 'users' table should be extended in a new migration.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
