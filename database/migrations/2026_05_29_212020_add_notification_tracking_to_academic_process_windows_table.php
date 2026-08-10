<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_process_windows', function (Blueprint $table) {
            $table->dateTime('opened_notification_sent_at')->nullable()->after('notes');
            $table->dateTime('closing_notification_sent_at')->nullable()->after('opened_notification_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_process_windows', function (Blueprint $table) {
            $table->dropColumn(['opened_notification_sent_at', 'closing_notification_sent_at']);
        });
    }
};
