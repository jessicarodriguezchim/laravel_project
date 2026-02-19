<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            ['name' => 'Cardiología'],
            ['name' => 'Pediatría'],
            ['name' => 'Dermatología'],
            ['name' => 'Neurología'],
            ['name' => 'Ginecología'],
            ['name' => 'Oftalmología'],
            ['name' => 'Traumatología'],
            ['name' => 'Psiquiatría'],
            ['name' => 'Medicina General'],
            ['name' => 'Endocrinología'],
        ];

        foreach ($specialities as $speciality) {
            Speciality::create($speciality);
        }
    }
}

