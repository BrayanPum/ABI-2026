<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_process_windows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_period_id')->constrained('academic_periods')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('process_key', 100);
            $table->string('name', 150)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('requires_evaluation')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['academic_period_id', 'process_key'], 'academic_process_windows_unique_period_process');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_process_windows');
    }
};
