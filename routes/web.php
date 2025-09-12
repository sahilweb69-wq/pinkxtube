<?php

use App\Http\Controllers\Settings;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

// Compatibility: preserve route name 'dashboard' but redirect to admin dashboard
Route::redirect('dashboard', '/admin/dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Backend (Admin) routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission:admin.access'])->group(function () {
    // Admin Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin Settings (for backend users only)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', [Settings\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [Settings\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [Settings\ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('password', [Settings\PasswordController::class, 'edit'])->name('password.edit');
        Route::put('password', [Settings\PasswordController::class, 'update'])->name('password.update');
        Route::get('appearance', [Settings\AppearanceController::class, 'edit'])->name('appearance.edit');
    });

    // Roles
    Route::resource('roles', RoleController::class);

    // Permissions
    Route::resource('permissions', PermissionController::class)->except(['show']);
});

