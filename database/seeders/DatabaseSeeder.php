<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cambiamos 'name' por 'nombre_usuario'
        \App\Models\User::factory()->create([
            'nombre_usuario' => 'Test User', 
            'email' => 'test@example.com',
        ]);

        // Y no olvides llamar a tu nuevo seeder de géneros aquí
        $this->call([
            GeneroSeeder::class,
        ]);
    }
}
