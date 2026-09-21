<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PipelineStageController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectMediaController;
use App\Http\Controllers\Admin\ProjectStageController;
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
    ->middleware(['auth', 'active', 'role'])
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

        // --- Pipeline management ------------------------------------------
        // The stages every project travels through. Editing the pipeline is
        // a configuration change, so it sits behind its own permission
        // rather than under projects.manage.
        Route::middleware('can:pipeline.view')->group(function () {
            Route::get('pipeline', [PipelineStageController::class, 'index'])->name('pipeline.index');
        });
        Route::middleware('can:pipeline.manage')->group(function () {
            Route::get('pipeline/create', [PipelineStageController::class, 'create'])->name('pipeline.create');
            Route::post('pipeline', [PipelineStageController::class, 'store'])->name('pipeline.store');
            Route::post('pipeline/reorder', [PipelineStageController::class, 'reorder'])->name('pipeline.reorder');
            Route::get('pipeline/{stage}/edit', [PipelineStageController::class, 'edit'])->name('pipeline.edit');
            Route::put('pipeline/{stage}', [PipelineStageController::class, 'update'])->name('pipeline.update');
            Route::delete('pipeline/{stage}', [PipelineStageController::class, 'destroy'])->name('pipeline.destroy');
        });

        // --- Client accounts ----------------------------------------------
        Route::middleware('can:clients.view')->group(function () {
            Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
            Route::get('clients/{client}', [ClientController::class, 'show'])
                ->whereNumber('client')->name('clients.show');
        });
        Route::middleware('can:clients.manage')->group(function () {
            Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
            Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
            Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
            Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
            // Activation / suspension — this is what sends the welcome mail.
            Route::put('clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.status');
            Route::post('clients/{client}/resend-invitation', [ClientController::class, 'resendInvitation'])
                ->name('clients.resend-invitation');
        });

        // --- Projects ------------------------------------------------------
        Route::middleware('can:projects.view')->group(function () {
            Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
            Route::get('projects/{project}', [ProjectController::class, 'show'])
                ->whereNumber('project')->name('projects.show');
        });
        Route::middleware('can:projects.manage')->group(function () {
            Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
            Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

            Route::put('projects/{project}/stage', [ProjectStageController::class, 'move'])->name('projects.stage.move');
            Route::put('projects/{project}/stages/{stage}', [ProjectStageController::class, 'updateEntry'])
                ->name('projects.stage.entry');

            Route::post('projects/{project}/media', [ProjectMediaController::class, 'store'])->name('projects.media.store');
            Route::delete('projects/{project}/media/{medium}', [ProjectMediaController::class, 'destroy'])
                ->name('projects.media.destroy');
        });
    });
