<?php

namespace Database\Seeders;

use App\Models\Permissions;
use Illuminate\Database\Seeder;

class RegisterPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        $permissionList = [
            // users
            ['name' => 'user-find', 'description' => 'Permissão para listar usuário por ID.', 'active' => true],
            ['name' => 'user-index', 'description' => 'Permissão para listar usuário.', 'active' => true],
            ['name' => 'user-pageable', 'description' => 'Permissão para listar usuários de forma páginada.', 'active' => true],
            ['name' => 'user-status', 'description' => 'Permissão para atualizar o status do usuário (ativo/inativo).', 'active' => true],
            ['name' => 'user-store', 'description' => 'Permissão para cadastrar novo usuário.', 'active' => true],
            ['name' => 'user-update', 'description' => 'Permissão para atualizar usuário existente.', 'active' => true],
            ['name' => 'user-destroy', 'description' => 'Permissão para deletar usuário por ID.', 'active' => true],
            // products
            ['name' => 'product-find', 'description' => 'Permissão para listar produto por ID.', 'active' => true],
            ['name' => 'product-index', 'description' => 'Permissão para listar produto.', 'active' => true],
            ['name' => 'product-pageable', 'description' => 'Permissão para listar produtos de forma páginada.', 'active' => true],
            ['name' => 'product-status', 'description' => 'Permissão para atualizar o status do produto (disponível/indisponível).', 'active' => true],
            ['name' => 'product-store', 'description' => 'Permissão para cadastrar novo produto.', 'active' => true],
            ['name' => 'product-update', 'description' => 'Permissão para atualizar produto existente.', 'active' => true],
            ['name' => 'product-upload', 'description' => 'Permissão para adicionar imagem ao produto.', 'active' => true],
            ['name' => 'product-destroy', 'description' => 'Permissão para deletar produto por ID.', 'active' => true],
            // categories
            ['name' => 'category-find', 'description' => 'Permissão para listar categoria por ID.', 'active' => true],
            ['name' => 'category-index', 'description' => 'Permissão para listar categoria.', 'active' => true],
            ['name' => 'category-pageable', 'description' => 'Permissão para listar categorias de forma páginada.', 'active' => true],
            ['name' => 'category-status', 'description' => 'Permissão para atualizar o status do categoria (disponível/indisponível).', 'active' => true],
            ['name' => 'category-store', 'description' => 'Permissão para cadastrar novo categoria.', 'active' => true],
            ['name' => 'category-update', 'description' => 'Permissão para atualizar categoria existente.', 'active' => true],
            ['name' => 'category-destroy', 'description' => 'Permissão para deletar categoria por ID.', 'active' => true],
            // roles
            ['name' => 'role-find', 'description' => 'Permissão para listar papel por ID.', 'active' => true],
            ['name' => 'role-index', 'description' => 'Permissão para listar papel.', 'active' => true],
            ['name' => 'role-pageable', 'description' => 'Permissão para listar papéis de forma páginada.', 'active' => true],
            ['name' => 'role-status', 'description' => 'Permissão para atualizar o status do papel (disponível/indisponível).', 'active' => true],
            ['name' => 'role-store', 'description' => 'Permissão para cadastrar novo papel.', 'active' => true],
            ['name' => 'role-update', 'description' => 'Permissão para atualizar papel existente.', 'active' => true],
            ['name' => 'role-destroy', 'description' => 'Permissão para deletar papel por ID.', 'active' => true],
        ];

        foreach ($permissionList as $role) {
            Permissions::firstOrCreate($role);
        }
    }
}
