<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea un usuario de prueba
        // El modelo User tiene cast 'hashed' en password, así que se hashea automáticamente
        $user = User::create([
            'name' => 'Jessica Rodriguez',
            'email' => 'jessica.rodriguez@tecdesoftware.com',
            'password' => '12345678',
            'email_verified_at' => now(),
            'id_number' => '123456789',
            'phone' => '5555555555',
            'address' => 'Calle 123, Colonia 456',
        ]);
        $user->assignRole('Doctor');
    }
}
