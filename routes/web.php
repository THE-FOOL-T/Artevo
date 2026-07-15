<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleUpgradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Only routes for modules that exist so far are registered here. Every
| later phase (museums, artifacts, exhibitions, auctions, ...) adds its
| own route group to this file rather than replacing what's here.
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');

// Rate limited: 5 submissions per minute per IP, to stop the form being
// used to spam the support inbox.
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
| The dashboard dispatches to a role-specific view (Phase 3). Its content
| is still a skeleton — real activity (museums, artifacts, auctions...)
| surfaces here as each of those modules is built in later phases.
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/become-collector', [RoleUpgradeController::class, 'store'])
        ->middleware('visitor')
        ->name('role-upgrade.store');
});

/*
|--------------------------------------------------------------------------
| Admin routes — Phase 3
|--------------------------------------------------------------------------
| Every route in this group is protected by the 'admin' middleware, not
| just hidden from the nav — visiting it directly without the role
| returns a 403, per the platform's "never rely on frontend checks" rule.
*/
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
});

require __DIR__ . '/auth.php';
