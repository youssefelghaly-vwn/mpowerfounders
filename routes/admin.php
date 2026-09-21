<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use Illuminate\Support\Facades\Route;

// Required from routes/web.php.
//
// Entry to /admin only requires being authenticated and holding *some*
// role — not specifically 'admin'. What a signed-in user can actually see
// and do inside is then gated per-section by permission, using Laravel's
// built-in 'can:' middleware. That works because AppServiceProvider
// registers a Gate::before() hook that treats every permission slug
// (roles.view, roles.manage, permissions.view, permissions.manage,
// users.view, users.manage-roles — see RolePermissionSeeder) as a
// checkable ability via $user->hasPermission().
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('can:roles.view')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        });
        Route::middleware('can:roles.manage')->group(function () {
            Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        Route::middleware('can:permissions.view')->group(function () {
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        });
        Route::middleware('can:permissions.manage')->group(function () {
            Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
            Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
            Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        Route::middleware('can:users.view')->group(function () {
            Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        });
        Route::middleware('can:users.manage-roles')->group(function () {
            Route::get('users/{user}/edit', [UserRoleController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserRoleController::class, 'update'])->name('users.update');
        });
    });