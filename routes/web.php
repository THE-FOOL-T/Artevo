<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\MuseumVerificationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ArtifactController;
use App\Http\Controllers\ArtifactDocumentController;
use App\Http\Controllers\ArtifactImageController;
use App\Http\Controllers\Collector\ArtifactController as CollectorArtifactController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Curator\ArtifactController as CuratorArtifactController;
use App\Http\Controllers\Curator\MuseumContactController;
use App\Http\Controllers\Curator\MuseumController as CuratorMuseumController;
use App\Http\Controllers\Curator\MuseumImageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MuseumController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleUpgradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Only routes for modules that exist so far are registered here. Every
| later phase (artifacts, exhibitions, auctions, ...) adds its own route
| group to this file rather than replacing what's here.
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
| Public museum directory — Phase 5
|--------------------------------------------------------------------------
| Anyone can browse without an account. Museum::getRouteKeyName() is
| 'slug', so {museum} binds by slug everywhere, including the curator
| management routes below.
*/
Route::get('/museums', [MuseumController::class, 'index'])->name('museums.index');
Route::get('/museums/{museum}', [MuseumController::class, 'show'])->name('museums.show');

/*
|--------------------------------------------------------------------------
| Public artifact directory — Phase 7
|--------------------------------------------------------------------------
| Basic browsing for now — the rich detail page, AJAX search/filter, and
| related-artifacts experience is Phase 8. Artifact::getRouteKeyName() is
| 'slug', binding {artifact} by slug everywhere, including the media
| management routes below.
*/
Route::get('/artifacts', [ArtifactController::class, 'index'])->name('artifacts.index');
Route::get('/artifacts/{artifact}', [ArtifactController::class, 'show'])->name('artifacts.show');

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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
| Every route in this group is protected by the 'admin' middleware, not
| just hidden from the nav — visiting it directly without the role
| returns a 403, per the platform's "never rely on frontend checks" rule.
*/
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::patch('/museums/{museum}/verification', [MuseumVerificationController::class, 'update'])->name('museums.verification.update');
});

/*
|--------------------------------------------------------------------------
| Curator museum management — Phase 5, extended in Phase 6
|--------------------------------------------------------------------------
| Deliberately NOT gated by the 'curator' middleware — an Administrator
| must also be able to manage any museum, and that middleware would
| block them. Fine-grained "own museum, or admin" authorization is
| handled by MuseumPolicy via each Form Request / controller action.
| Verification status changes are admin-only and live in the admin
| group above instead (Admin\MuseumVerificationController).
*/
Route::middleware(['auth', 'verified'])->prefix('curator/museums')->name('curator.museums.')->group(function () {
    Route::get('/', [CuratorMuseumController::class, 'index'])->name('index');
    Route::get('/create', [CuratorMuseumController::class, 'create'])->name('create');
    Route::post('/', [CuratorMuseumController::class, 'store'])->name('store');
    Route::get('/{museum}/edit', [CuratorMuseumController::class, 'edit'])->name('edit');
    Route::get('/{museum}/dashboard', [CuratorMuseumController::class, 'dashboard'])->name('dashboard');
    Route::put('/{museum}', [CuratorMuseumController::class, 'update'])->name('update');
    Route::delete('/{museum}', [CuratorMuseumController::class, 'destroy'])->name('destroy');

    Route::post('/{museum}/images', [MuseumImageController::class, 'store'])->name('images.store');
    Route::delete('/{museum}/images/{image}', [MuseumImageController::class, 'destroy'])->name('images.destroy');

    Route::post('/{museum}/contacts', [MuseumContactController::class, 'store'])->name('contacts.store');
    Route::delete('/{museum}/contacts/{contact}', [MuseumContactController::class, 'destroy'])->name('contacts.destroy');

    // Artifacts belonging to this specific museum's collection — Phase 7.
    Route::prefix('/{museum}/artifacts')->name('artifacts.')->group(function () {
        Route::get('/', [CuratorArtifactController::class, 'index'])->name('index');
        Route::get('/create', [CuratorArtifactController::class, 'create'])->name('create');
        Route::post('/', [CuratorArtifactController::class, 'store'])->name('store');
        Route::get('/{artifact}/edit', [CuratorArtifactController::class, 'edit'])->name('edit');
        Route::put('/{artifact}', [CuratorArtifactController::class, 'update'])->name('update');
        Route::delete('/{artifact}', [CuratorArtifactController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Collector artifact management — Phase 7
|--------------------------------------------------------------------------
| A collector's personal collection isn't nested under anything — unlike
| a curator's artifacts, which belong to a specific museum.
*/
Route::middleware(['auth', 'verified'])->prefix('collector/artifacts')->name('collector.artifacts.')->group(function () {
    Route::get('/', [CollectorArtifactController::class, 'index'])->name('index');
    Route::get('/create', [CollectorArtifactController::class, 'create'])->name('create');
    Route::post('/', [CollectorArtifactController::class, 'store'])->name('store');
    Route::get('/{artifact}/edit', [CollectorArtifactController::class, 'edit'])->name('edit');
    Route::put('/{artifact}', [CollectorArtifactController::class, 'update'])->name('update');
    Route::delete('/{artifact}', [CollectorArtifactController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Shared artifact media routes — Phase 7
|--------------------------------------------------------------------------
| Used by both the curator and collector flows. Not nested under either
| prefix since authorization is fully handled by ArtifactPolicy checking
| ownership directly, regardless of which flow the artifact came from.
*/
Route::middleware(['auth', 'verified'])->prefix('artifacts/{artifact}')->name('artifacts.')->group(function () {
    Route::post('/images', [ArtifactImageController::class, 'store'])->name('images.store');
    Route::delete('/images/{image}', [ArtifactImageController::class, 'destroy'])->name('images.destroy');
    Route::patch('/images/{image}/primary', [ArtifactImageController::class, 'makePrimary'])->name('images.primary');

    Route::post('/documents', [ArtifactDocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [ArtifactDocumentController::class, 'destroy'])->name('documents.destroy');
});

require __DIR__ . '/auth.php';
