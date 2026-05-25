<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formato_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formato_registro_id')->constrained('formato_registros')->cascadeOnDelete();
            $table->foreignId('formato_campo_id')->constrained('formato_campos')->cascadeOnDelete();
            $table->text('valor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_valores');
    }
};
