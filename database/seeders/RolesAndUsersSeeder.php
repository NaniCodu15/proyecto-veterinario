<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin' => Role::firstOrCreate(['name' => 'admin']),
            'asistente' => Role::firstOrCreate(['name' => 'asistente']),
        ];

        $admin = User::where('email', 'Dra@Alfaro.com')->first();

        if ($admin) {
            $admin->role()->associate($roles['admin']);
            $admin->save();
        }

        User::firstOrCreate(
            ['email' => 'asistente@gmail.com'],
            [
                'name' => 'Asistente',
                'password' => Hash::make('Asistente123'),
                'role_id' => $roles['asistente']->id,
            ]
        );
    }
}
