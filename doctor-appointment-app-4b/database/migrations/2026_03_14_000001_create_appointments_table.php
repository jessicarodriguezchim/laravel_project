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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Relación con paciente
            $table->foreignId('patient_id')
                ->constrained()
                ->onDelete('cascade');

            // Relación con doctor
            $table->foreignId('doctor_id')
                ->constrained()
                ->onDelete('cascade');

            // Fecha de la cita
            $table->date('date');

            // Hora de inicio
            $table->time('start_time');

            // Hora de fin
            $table->time('end_time');

            // Duración en minutos (por defecto 15)
            $table->integer('duration')->default(15);

            // Motivo de la cita
            $table->text('reason')->nullable();

            // Estado de la cita (1=Pendiente, 2=Confirmada, 3=Completada, 4=Cancelada)
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
