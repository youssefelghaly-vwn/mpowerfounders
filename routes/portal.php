<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ProjectController;
use App\Http\Controllers\Portal\ProjectMediaController;
use Illuminate\Support\Facades\Route;

// Required from routes/web.php.
//
// The client-facing side of the app. Three gates, in order: signed in
// ('auth'), account actually activated ('active' — a pending or suspended
// account is bounced back to the sign-in page with an explanation), and
// holding the 'client' role, which activation is what attaches.
Route::prefix('portal')
    ->name('portal.')
    ->middleware(['auth', 'active', 'role:client'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->whereNumber('project')->name('projects.show');

        Route::post('projects/{project}/media', [ProjectMediaController::class, 'store'])->name('projects.media.store');
        Route::delete('projects/{project}/media/{medium}', [ProjectMediaController::class, 'destroy'])
            ->name('projects.media.destroy');
    });

// Playback and downloads for both audiences — staff and clients hit the
// same two routes and the ProjectMediaPolicy decides. Outside the /portal
// group on purpose: staff are not clients and must be able to open a file
// from the admin panel.
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('media/{medium}', [MediaController::class, 'show'])->name('media.show');
    Route::get('media/{medium}/download', [MediaController::class, 'download'])->name('media.download');
});
