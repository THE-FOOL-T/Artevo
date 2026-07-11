<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Phase 1: Foundation
|--------------------------------------------------------------------------
| Only routes for modules that exist so far are registered here. Every
| later phase (auth, museums, artifacts, exhibitions, auctions, ...) adds
| its own route group to this file rather than replacing what's here.
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
