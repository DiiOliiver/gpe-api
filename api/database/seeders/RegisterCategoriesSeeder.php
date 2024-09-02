<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;

class RegisterCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        $roles = [
            ['name' => 'Refrigeradores', 'active' => true],
            ['name' => 'Máquinas de Lavar', 'active' => true],
            ['name' => 'Fogões e Fornos', 'active' => true],
            ['name' => 'Aparelhos de Ar Condicionado', 'active' => true],
            ['name' => 'Pequenos Eletrodomésticos', 'active' => true],
        ];

        foreach ($roles as $role) {
            Categories::firstOrCreate($role);
        }
    }
}
