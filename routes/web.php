<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
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
| Authenticated routes — Phase 2
|--------------------------------------------------------------------------
| The dashboard is a generic placeholder for now — role-specific
| dashboard content (Administrator/Curator/Collector/Visitor) is built in
| Phase 3 (User & Role Management) and later phases, on this same route.
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
