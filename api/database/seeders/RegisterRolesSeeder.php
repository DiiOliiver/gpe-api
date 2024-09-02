<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Seeder;

class RegisterRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'active' => true],
            ['name' => 'Gerente de Estoque', 'active' => true],
            ['name' => 'Operador de Estoque', 'active' => true],
            ['name' => 'Visualizador', 'active' => true],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate($role);
        }
    }
}
