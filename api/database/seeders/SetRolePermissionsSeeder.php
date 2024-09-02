<?php

namespace Database\Seeders;

use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class SetRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        $roles = Roles::query('active', true)->get();

        $admin = $roles->first(function ($role) {
            return $role->name === 'Administrador';
        });
        self::setPermitioonsToAdministrator($admin);

        $stockManager = $roles->first(function ($role) {
            return $role->name === 'Gerente de Estoque';
        });
        self::setPermitioonsToStockManager($stockManager);

        $stockOperator = $roles->first(function ($role) {
            return $role->name === 'Operador de Estoque';
        });
        self::setPermitioonsToStockOperator($stockOperator);

        $viewer = $roles->first(function ($role) {
            return $role->name === 'Visualizador';
        });
        self::setPermitioonsToViewer($viewer);
    }

    public static function setPermitioonsToAdministrator(Roles $admin): void
    {
        $permitions = Permissions::where('active', true)->pluck('id')->toArray();
        $admin->permissions()->sync($permitions);
    }

    public static function setPermitioonsToStockManager(Roles $stockManager): void
    {
        $permitions = Permissions::where('active', true)
            ->whereIn('name', [
                'product-index', 'product-find', 'product-pageable', 'product-status', 'product-store', 'product-update', 'product-upload',
                'category-index', 'category-find', 'category-pageable', 'category-status', 'category-store', 'category-update'
            ])
            ->pluck('id')->toArray();
        $stockManager->permissions()->sync($permitions);
    }

    public static function setPermitioonsToStockOperator(Roles $stockOperator): void
    {
        $permitions = Permissions::where('active', true)
            ->whereIn('name', [
                'product-index', 'product-find', 'product-pageable', 'product-status', 'product-upload',
            ])
            ->pluck('id')->toArray();
        $stockOperator->permissions()->sync($permitions);
    }

    public static function setPermitioonsToViewer(Roles $stockManager): void
    {
        $permitions = Permissions::where('active', true)
            ->whereIn('name', [
                'product-index', 'product-find', 'product-pageable'
            ])
            ->pluck('id')->toArray();
        $stockManager->permissions()->sync($permitions);
    }
}
