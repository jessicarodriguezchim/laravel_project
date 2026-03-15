<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'appointment_id',
        'diagnosis',
        'treatment',
        'notes',
        'prescription',
    ];

    protected $casts = [
        'prescription' => 'array',
    ];

    // Relación: Una consulta pertenece a una cita
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Acceso directo al paciente a través de la cita
    public function getPatientAttribute()
    {
        return $this->appointment->patient;
    }

    // Acceso directo al doctor a través de la cita
    public function getDoctorAttribute()
    {
        return $this->appointment->doctor;
    }
}
