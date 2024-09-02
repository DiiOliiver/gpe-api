<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('SEEDER')) {
            RegisterRolesSeeder::run();
            RegisterPermissionsSeeder::run();
            SetRolePermissionsSeeder::run();
            RegisterCategoriesSeeder::run();

            $user = User::firstWhere('email', 'diego.feitosa@example.com');
            if ($user === null) {
                $role = Roles::where('name', 'Administrador')->first();

                User::firstOrCreate([
                    'name' => 'Diego Feitosa',
                    'email' => 'diego.feitosa@example.com',
                    'password' => Hash::make('admin'),
                    'active' => true,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
}
