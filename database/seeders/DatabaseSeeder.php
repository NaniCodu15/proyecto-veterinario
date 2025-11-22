<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crea o actualiza un usuario administrador por defecto para facilitar el acceso inicial.
        User::updateOrCreate(
            ['email' => env('DEFAULT_ADMIN_EMAIL', 'Dra@Alfaro.com')],
            [
                'name' => 'Rosalia Alfaro',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', 'Admin123')),
            ]
        );
    }
}