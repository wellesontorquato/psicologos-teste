<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria um usuário admin
        User::factory()->create([
            'name' => 'Admin Teste',
            'email' => 'admin@eraixample.com',
            'password' => bcrypt('12345678'), // senha padrão
        ]);
    }
}
