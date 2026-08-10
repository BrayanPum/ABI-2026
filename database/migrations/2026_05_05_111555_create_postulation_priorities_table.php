<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulation_priorities', function (Blueprint $table) {
            $table->id('priority_id');

            // El estudiante que asigna la prioridad
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('cascade');

            // A qué postulación le asigna prioridad
            $table->unsignedBigInteger('postulation_id');
            $table->foreign('postulation_id')
                ->references('postulation_id')->on('postulations')
                ->onDelete('cascade');

            // 1 = primera opción, 2 = segunda, 3 = tercera
            $table->tinyInteger('priority_order')->unsigned();

            $table->timestamps();

            // No puede tener dos postulaciones con la misma prioridad
            $table->unique(['student_id', 'priority_order'], 'uq_priority_order');

            // No puede priorizar la misma postulación dos veces
            $table->unique(['student_id', 'postulation_id'], 'uq_priority_postulation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulation_priorities');
    }
};
