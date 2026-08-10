<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulations', function (Blueprint $table) {
            $table->id('postulation_id');

            // A qué idea se postula
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');

            // Quién lidera la postulación
            $table->unsignedBigInteger('lead_student_id');
            $table->foreign('lead_student_id')
                ->references('id')->on('students')
                ->onDelete('cascade');

            // Contenido de la postulación
            $table->text('justification');
            $table->enum('modality', ['individual', 'team'])->default('individual');
            $table->boolean('accepted_terms')->default(false);
            $table->string('grades_file', 500);

            // Estado
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // Revisión del profesor o comité
            $table->text('review_comment')->nullable();

            $table->timestamps();

            // Un estudiante no puede postularse dos veces a la misma idea
            $table->unique(['project_id', 'lead_student_id'], 'uq_postulation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulations');
    }
};
