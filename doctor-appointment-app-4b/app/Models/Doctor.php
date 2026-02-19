<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'speciality_id',
        'license_number',
        'biography',
    ];

    // Relación uno a uno inversa: Un doctor pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación muchos a uno: Un doctor pertenece a una especialidad
    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }
}

