<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'reason',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'integer',
        'duration' => 'integer',
    ];

    // Constantes para los estados
    const STATUS_PENDING = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 4;

    // Relación: Una cita pertenece a un paciente
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relación: Una cita pertenece a un doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Relación: Una cita puede tener una consulta
    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    // Obtener el nombre del estado
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_CANCELLED => 'Cancelada',
            default => 'Desconocido',
        };
    }

    // Obtener el color del badge según el estado
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }
}
