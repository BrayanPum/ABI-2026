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
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_postulation_id')->nullable()->after('project_status_id');
            $table->foreign('approved_postulation_id', 'fk_projects_postulation')
                ->references('postulation_id')->on('postulations')
                ->onDelete('set null');
        });

        Schema::table('student_project', function (Blueprint $table) {
            $table->unsignedBigInteger('postulation_id')->nullable()->after('project_id');
            $table->foreign('postulation_id', 'fk_student_project_postulation')
                ->references('postulation_id')->on('postulations')
                ->onDelete('set null');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('postulation_id')->nullable()->after('user_id');
            $table->foreign('postulation_id', 'fk_students_postulation')
                ->references('postulation_id')->on('postulations')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('fk_students_postulation');
            $table->dropColumn('postulation_id');
        });

        Schema::table('student_project', function (Blueprint $table) {
            $table->dropForeign('fk_student_project_postulation');
            $table->dropColumn('postulation_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('fk_projects_postulation');
            $table->dropColumn('approved_postulation_id');
        });
    }
};
