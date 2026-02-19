<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $fillable = ['name'];

    // Relación uno a muchos: Una especialidad tiene muchos doctores
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}

