<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulation_members', function (Blueprint $table) {
            $table->id('member_id');

            // A qué postulación pertenece
            $table->unsignedBigInteger('postulation_id');
            $table->foreign('postulation_id')
                ->references('postulation_id')->on('postulations')
                ->onDelete('cascade');

            // Quién es el integrante
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('cascade');

            $table->text('role_description');
            $table->boolean('is_lead')->default(false);

            $table->timestamps();

            // Un estudiante no puede aparecer dos veces en la misma postulación
            $table->unique(['postulation_id', 'student_id'], 'uq_member');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulation_members');
    }
};
