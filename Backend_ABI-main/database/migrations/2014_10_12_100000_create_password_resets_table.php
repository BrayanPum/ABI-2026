<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * Migration to create the password_resets table.
 *
 * Table created:
 * - password_resets: Stores password reset tokens linked to user emails.
 *
 * Key details:
 * - 'email' is indexed to allow fast lookups when validating reset requests.
 * - Tokens are stored as plain strings, with a timestamp for expiration handling.
 *
 * Note: This table is part of Laravel's legacy password reset system.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};
