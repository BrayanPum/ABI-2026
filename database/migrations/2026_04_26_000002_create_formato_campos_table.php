<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formato_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formato_tipo_id')->constrained('formato_tipos')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('etiqueta');
            $table->enum('tipo', ['texto', 'numero', 'fecha', 'hora', 'select', 'checkbox', 'textarea']);
            $table->json('opciones')->nullable();
            $table->boolean('requerido')->default(false);
            $table->string('seccion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formato_campos');
    }
};
