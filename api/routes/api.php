<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\roles\RolesController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\products\ProductsController;
use App\Http\Controllers\categories\CategoriesController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum','userIsActive'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/roles/pageable', [RolesController::class, 'pageable']);
        Route::get('/roles/{id}', [RolesController::class, 'findById']);

        Route::get('/users/pageable', [UserController::class, 'pageable']);
        Route::get('/users/{id}', [UserController::class, 'findById']);

        Route::get('/products/pageable', [ProductsController::class, 'pageable']);
        Route::get('/products/{id}', [ProductsController::class, 'findById']);
        Route::post('/products/{id}/upload', [ProductsController::class, 'upload']);

        Route::get('/categories', [CategoriesController::class, 'index']);
        Route::get('/categories/pageable', [CategoriesController::class, 'pageable']);

        Route::apiResources([
            'roles' => RolesController::class,
            'users' => UserController::class,
            'products' => ProductsController::class,
        ]);
    });
});
