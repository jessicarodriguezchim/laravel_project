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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            // Relación con la cita
            $table->foreignId('appointment_id')
                ->constrained()
                ->onDelete('cascade');

            // Diagnóstico médico
            $table->text('diagnosis')->nullable();

            // Tratamiento recomendado
            $table->text('treatment')->nullable();

            // Notas adicionales
            $table->text('notes')->nullable();

            // Receta médica (JSON con array de medicamentos)
            $table->json('prescription')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
