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
        User::updateOrCreate(
            ['email' => 'doctora@example.com'],
            [
                'name' => 'Dra. Administradora',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'asistente@example.com'],
            [
                'name' => 'Asistente Clínica',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ASISTENTE,
            ]
        );
    }
}
