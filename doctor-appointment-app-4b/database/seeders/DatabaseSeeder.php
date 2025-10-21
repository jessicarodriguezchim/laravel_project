<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ullamar a RoleSeeder
        $this->call([
            RoleSeeder::class
        ]);

        //crea un usuario de prueba cada que se ejecuten migraciones 
        //php artisan migrate:fresh -seed

        User::factory()->create([
            'name' => 'Jessica Rodriguez',
            'email' => 'jessica.rodriguez@tecdesoftware.com',
            'password' => bcrypt('12345678')
        ]);
    }
}
