<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArtifactVerificationController as AdminArtifactVerificationController;
use App\Http\Controllers\Admin\MuseumVerificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ArtifactController;
use App\Http\Controllers\ArtifactDocumentController;
use App\Http\Controllers\ArtifactImageController;
use App\Http\Controllers\ArtifactProvenanceController;
use App\Http\Controllers\ArtifactVerificationController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\CuratorNoteController;
use App\Http\Controllers\RestorationRecordController;
use App\Http\Controllers\Collector\ArtifactController as CollectorArtifactController;
use App\Http\Controllers\Collector\AuctionController as CollectorAuctionController;
use App\Http\Controllers\Collector\CollectionController as CollectorCollectionController;
use App\Http\Controllers\CollectionArtifactController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionFavoriteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Curator\ArtifactController as CuratorArtifactController;
use App\Http\Controllers\Curator\AuctionController as CuratorAuctionController;
use App\Http\Controllers\Curator\CollectionController as CuratorCollectionController;
use App\Http\Controllers\Curator\ExhibitionController as CuratorExhibitionController;
use App\Http\Controllers\Curator\ExhibitionSectionArtifactController;
use App\Http\Controllers\Curator\ExhibitionSectionController;
use App\Http\Controllers\Curator\MuseumContactController;
use App\Http\Controllers\Curator\MuseumController as CuratorMuseumController;
use App\Http\Controllers\Curator\MuseumImageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExhibitionController;
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

    Route::post('/profile/api-tokens', [ApiTokenController::class, 'store'])->name('profile.api-tokens.store');
    Route::delete('/profile/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('profile.api-tokens.destroy');

    Route::post('/become-collector', [RoleUpgradeController::class, 'store'])
        ->middleware('visitor')
        ->name('role-upgrade.store');

    Route::get('/curator-applications/create', [\App\Http\Controllers\CuratorApplicationController::class, 'create'])
        ->name('curator-applications.create');
    Route::post('/curator-applications', [\App\Http\Controllers\CuratorApplicationController::class, 'store'])
        ->name('curator-applications.store');

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
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{type}/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/curator-applications', [\App\Http\Controllers\Admin\CuratorApplicationController::class, 'index'])->name('curator-applications.index');
    Route::patch('/curator-applications/{application}', [\App\Http\Controllers\Admin\CuratorApplicationController::class, 'update'])->name('curator-applications.update');
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

/*
|--------------------------------------------------------------------------
| Public collection browser — Phase 9
|--------------------------------------------------------------------------
| Collection::getRouteKeyName() is 'slug', so {collection} binds by slug
| everywhere. Anyone may browse public collections without an account.
*/
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');

/*
|--------------------------------------------------------------------------
| Curator museum collections — Phase 9
|--------------------------------------------------------------------------
| Nested under /curator/museums/{museum} so a curator always operates
| in the context of a specific museum. Fine-grained authorization is
| handled by CollectionPolicy (not just the curator middleware).
*/
Route::middleware(['auth', 'verified'])
    ->prefix('curator/museums/{museum}/collections')
    ->name('curator.collections.')
    ->group(function () {
        Route::get('/', [CuratorCollectionController::class, 'index'])->name('index');
        Route::get('/create', [CuratorCollectionController::class, 'create'])->name('create');
        Route::post('/', [CuratorCollectionController::class, 'store'])->name('store');
        Route::get('/{collection}/edit', [CuratorCollectionController::class, 'edit'])->name('edit');
        Route::put('/{collection}', [CuratorCollectionController::class, 'update'])->name('update');
        Route::delete('/{collection}', [CuratorCollectionController::class, 'destroy'])->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Collector personal collections — Phase 9
|--------------------------------------------------------------------------
| Not nested under a museum — a collector's collections are their own.
*/
Route::middleware(['auth', 'verified'])
    ->prefix('collector/collections')
    ->name('collector.collections.')
    ->group(function () {
        Route::get('/', [CollectorCollectionController::class, 'index'])->name('index');
        Route::get('/create', [CollectorCollectionController::class, 'create'])->name('create');
        Route::post('/', [CollectorCollectionController::class, 'store'])->name('store');
        Route::get('/{collection}/edit', [CollectorCollectionController::class, 'edit'])->name('edit');
        Route::put('/{collection}', [CollectorCollectionController::class, 'update'])->name('update');
        Route::delete('/{collection}', [CollectorCollectionController::class, 'destroy'])->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Shared collection artifact-management & favorites — Phase 9
|--------------------------------------------------------------------------
| Not nested under either the curator or collector prefix — authorization
| is fully handled by CollectionPolicy, regardless of which flow created
| the collection.
*/
Route::middleware(['auth', 'verified'])
    ->prefix('collections/{collection}')
    ->name('collections.')
    ->group(function () {
        Route::post('/artifacts', [CollectionArtifactController::class, 'store'])->name('artifacts.store');
        Route::delete('/artifacts/{artifact}', [CollectionArtifactController::class, 'destroy'])->name('artifacts.destroy');
        Route::post('/artifacts/reorder', [CollectionArtifactController::class, 'reorder'])->name('artifacts.reorder');

        Route::post('/favorite', [CollectionFavoriteController::class, 'store'])->name('favorite');
        Route::delete('/favorite', [CollectionFavoriteController::class, 'destroy'])->name('unfavorite');
    });

/*
|--------------------------------------------------------------------------
| Favorites Dashboard & Toggles
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    
    Route::post('/artifacts/{artifact}/favorite', [\App\Http\Controllers\ArtifactFavoriteController::class, 'store'])->name('artifacts.favorite.store');
    Route::delete('/artifacts/{artifact}/favorite', [\App\Http\Controllers\ArtifactFavoriteController::class, 'destroy'])->name('artifacts.favorite.destroy');
    
    Route::post('/exhibitions/{exhibition}/favorite', [\App\Http\Controllers\ExhibitionFavoriteController::class, 'store'])->name('exhibitions.favorite.store');
    Route::delete('/exhibitions/{exhibition}/favorite', [\App\Http\Controllers\ExhibitionFavoriteController::class, 'destroy'])->name('exhibitions.favorite.destroy');
});

/*
|--------------------------------------------------------------------------
| Exhibitions — Phase 10
|--------------------------------------------------------------------------
*/

// Public exhibition browsing
Route::get('/exhibitions', [ExhibitionController::class, 'index'])->name('exhibitions.index');
Route::get('/exhibitions/{exhibition}', [ExhibitionController::class, 'show'])->name('exhibitions.show');

// Curator museum-scoped exhibition CRUD
Route::middleware(['auth', 'verified', 'curator'])
    ->prefix('curator/museums/{museum}/exhibitions')
    ->name('curator.exhibitions.')
    ->group(function () {
        Route::get('/', [CuratorExhibitionController::class, 'index'])->name('index');
        Route::get('/create', [CuratorExhibitionController::class, 'create'])->name('create');
        Route::post('/', [CuratorExhibitionController::class, 'store'])->name('store');
        Route::get('/{exhibition}/edit', [CuratorExhibitionController::class, 'edit'])->name('edit');
        Route::put('/{exhibition}', [CuratorExhibitionController::class, 'update'])->name('update');
        Route::delete('/{exhibition}', [CuratorExhibitionController::class, 'destroy'])->name('destroy');
        Route::patch('/{exhibition}/publish', [CuratorExhibitionController::class, 'publish'])->name('publish');
        Route::patch('/{exhibition}/archive', [CuratorExhibitionController::class, 'archive'])->name('archive');
    });

// Curator: section management (AJAX — not museum-nested because section ID is enough)
Route::middleware(['auth', 'verified', 'curator'])
    ->prefix('curator/exhibitions/{exhibition}/sections')
    ->name('curator.exhibition-sections.')
    ->group(function () {
        Route::post('/', [ExhibitionSectionController::class, 'store'])->name('store');
        Route::put('/{section}', [ExhibitionSectionController::class, 'update'])->name('update');
        Route::delete('/{section}', [ExhibitionSectionController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [ExhibitionSectionController::class, 'reorder'])->name('reorder');
    });

// Curator: artifact-in-section management (AJAX)
Route::middleware(['auth', 'verified', 'curator'])
    ->prefix('curator/exhibition-sections/{section}')
    ->name('curator.section-artifacts.')
    ->group(function () {
        Route::post('/artifacts', [ExhibitionSectionArtifactController::class, 'store'])->name('store');
        Route::delete('/artifacts/{artifact}', [ExhibitionSectionArtifactController::class, 'destroy'])->name('destroy');
        Route::post('/artifacts/reorder', [ExhibitionSectionArtifactController::class, 'reorder'])->name('reorder');
    });

/*
|--------------------------------------------------------------------------
| Artifact Verification & Provenance — Phase 11
|--------------------------------------------------------------------------
*/

// Owner submits their artifact for admin review
Route::middleware(['auth', 'verified'])
    ->post('/artifacts/{artifact}/verify-request', [ArtifactVerificationController::class, 'store'])
    ->name('artifacts.verify-request');

// Admin verification queue + decisions
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin/artifact-verifications')
    ->name('admin.artifact-verifications.')
    ->group(function () {
        Route::get('/', [AdminArtifactVerificationController::class, 'index'])->name('index');
        Route::post('/{artifact}/verify', [AdminArtifactVerificationController::class, 'verify'])->name('verify');
        Route::post('/{artifact}/reject', [AdminArtifactVerificationController::class, 'reject'])->name('reject');
    });

// Artifact provenance CRUD + reorder (owner or admin, AJAX-friendly)
Route::middleware(['auth', 'verified'])
    ->prefix('artifacts/{artifact}/provenance')
    ->name('artifacts.provenance.')
    ->group(function () {
        Route::post('/', [ArtifactProvenanceController::class, 'store'])->name('store');
        Route::put('/{provenance}', [ArtifactProvenanceController::class, 'update'])->name('update');
        Route::delete('/{provenance}', [ArtifactProvenanceController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [ArtifactProvenanceController::class, 'reorder'])->name('reorder');
    });

/*
|--------------------------------------------------------------------------
| Curator Notes & Restoration Records — Phase 12
|--------------------------------------------------------------------------
*/

// Private curator notes (owner or admin)
Route::middleware(['auth', 'verified'])
    ->prefix('artifacts/{artifact}/notes')
    ->name('artifacts.notes.')
    ->group(function () {
        Route::post('/', [CuratorNoteController::class, 'store'])->name('store');
        Route::put('/{note}', [CuratorNoteController::class, 'update'])->name('update');
        Route::delete('/{note}', [CuratorNoteController::class, 'destroy'])->name('destroy');
        Route::post('/{note}/pin', [CuratorNoteController::class, 'pin'])->name('pin');
    });

// Public restoration records (owner manages, all can view)
Route::middleware(['auth', 'verified'])
    ->prefix('artifacts/{artifact}/restoration')
    ->name('artifacts.restoration.')
    ->group(function () {
        Route::post('/', [RestorationRecordController::class, 'store'])->name('store');
        Route::put('/{record}', [RestorationRecordController::class, 'update'])->name('update');
        Route::delete('/{record}', [RestorationRecordController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [RestorationRecordController::class, 'reorder'])->name('reorder');
    });

/*
|--------------------------------------------------------------------------
| Auctions — Phase 14
|--------------------------------------------------------------------------
*/

// Public auction browsing and bidding
Route::prefix('auctions')->name('auctions.')->group(function () {
    Route::get('/', [AuctionController::class, 'index'])->name('index');
    Route::get('/{auction}', [AuctionController::class, 'show'])->name('show');
    Route::post('/{auction}/bid', [AuctionController::class, 'bid'])
        ->middleware(['auth', 'verified'])
        ->name('bid');
        
    Route::post('/{auction}/watch', [\App\Http\Controllers\AuctionWatcherController::class, 'store'])
        ->middleware(['auth', 'verified'])
        ->name('watch.store');
    Route::delete('/{auction}/watch', [\App\Http\Controllers\AuctionWatcherController::class, 'destroy'])
        ->middleware(['auth', 'verified'])
        ->name('watch.destroy');
});

// Curator auction management (within museum/artifact context)
Route::middleware(['auth', 'verified', 'curator'])
    ->prefix('curator')
    ->name('curator.')
    ->group(function () {
        Route::get(
            'museums/{museum}/artifacts/{artifact}/auction/create',
            [CuratorAuctionController::class, 'create']
        )->name('artifact-auction.create');

        Route::post(
            'museums/{museum}/artifacts/{artifact}/auction',
            [CuratorAuctionController::class, 'store']
        )->name('artifact-auction.store');

        Route::patch('auctions/{auction}/publish',  [CuratorAuctionController::class, 'publish'])->name('auctions.publish');
        Route::patch('auctions/{auction}/close',    [CuratorAuctionController::class, 'close'])->name('auctions.close');
        Route::delete('auctions/{auction}',         [CuratorAuctionController::class, 'cancel'])->name('auctions.cancel');
    });

// Collector auction management
Route::middleware(['auth', 'verified', 'collector'])
    ->prefix('collector')
    ->name('collector.')
    ->group(function () {
        Route::get(
            'artifacts/{artifact}/auction/create',
            [CollectorAuctionController::class, 'create']
        )->name('artifact-auction.create');

        Route::post(
            'artifacts/{artifact}/auction',
            [CollectorAuctionController::class, 'store']
        )->name('artifact-auction.store');

        Route::patch('auctions/{auction}/publish',  [CollectorAuctionController::class, 'publish'])->name('auctions.publish');
        Route::patch('auctions/{auction}/close',    [CollectorAuctionController::class, 'close'])->name('auctions.close');
        Route::delete('auctions/{auction}',         [CollectorAuctionController::class, 'cancel'])->name('auctions.cancel');

        Route::get('auctions/watchlist', [CollectorAuctionController::class, 'watchlist'])->name('auctions.watchlist');
        Route::get('auctions/bids', [CollectorAuctionController::class, 'bids'])->name('auctions.bids');
    });

/*
|--------------------------------------------------------------------------
| Donations — Phase 14
|--------------------------------------------------------------------------
| Donor-facing routes: browse own requests, submit, view, cancel.
| Admin routes: review queue, approve/reject, trigger ownership transfer.
*/

// Donor-facing donation routes
Route::middleware(['auth', 'verified'])
    ->prefix('donations')
    ->name('donations.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\DonationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DonationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DonationController::class, 'store'])->name('store');
        Route::get('/{donation}', [\App\Http\Controllers\DonationController::class, 'show'])->name('show');
        Route::delete('/{donation}', [\App\Http\Controllers\DonationController::class, 'destroy'])->name('destroy');
    });

// Admin donation management
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin/donations')
    ->name('admin.donations.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('index');
        Route::get('/{donation}', [\App\Http\Controllers\Admin\DonationController::class, 'show'])->name('show');
        Route::post('/{donation}/review', [\App\Http\Controllers\Admin\DonationController::class, 'review'])->name('review');
        Route::post('/{donation}/transfer', [\App\Http\Controllers\Admin\DonationController::class, 'transfer'])->name('transfer');
    });

/*
|--------------------------------------------------------------------------
| QR Codes — Phase 15
|--------------------------------------------------------------------------
| Public: /qr/{token} → scan log → redirect to artifact (no auth needed)
| Authenticated: download PNG, embed SVG (any signed-in user who can view)
| Admin: management overview, token regeneration
*/

// Public QR scan handler (no auth — anyone scanning a physical label)
Route::get('/qr/{token}', [\App\Http\Controllers\QrCodeController::class, 'scan'])
    ->name('qr.scan');

// Auth QR download / embed
Route::middleware(['auth', 'verified'])
    ->prefix('artifacts/{artifact}')
    ->name('artifacts.')
    ->group(function () {
        Route::get('/qr/download', [\App\Http\Controllers\QrCodeController::class, 'download'])->name('qr.download');
        Route::get('/qr/embed', [\App\Http\Controllers\QrCodeController::class, 'embed'])->name('qr.embed');
    });

// Admin QR management
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/qr-codes', [\App\Http\Controllers\Admin\QrCodeController::class, 'index'])->name('qr-codes.index');
        Route::post('/artifacts/{artifact}/qr/regenerate', [\App\Http\Controllers\Admin\QrCodeController::class, 'regenerate'])->name('artifacts.qr.regenerate');
    });

/*
|--------------------------------------------------------------------------
| Certificates of Authenticity — Phase 16
|--------------------------------------------------------------------------
| Public: /certificates/{serial} — anyone can verify a certificate
| Auth:   /certificates            — user's own certificates list
|         /artifacts/{artifact}/certificate/download
|         /artifacts/{artifact}/certificate/issue
| Admin:  /admin/certificates      — manage + revoke
*/

// Public certificate verification (no auth required)
Route::get('/certificates/{certificate}', [\App\Http\Controllers\CertificateController::class, 'verify'])
    ->name('certificates.verify');

// Authenticated certificate actions
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/certificates', [\App\Http\Controllers\CertificateController::class, 'index'])
        ->name('certificates.index');

    Route::get('/certificates/{certificate}/download', [\App\Http\Controllers\CertificateController::class, 'download'])
        ->name('certificates.download');

    Route::post('/artifacts/{artifact}/certificate/issue', [\App\Http\Controllers\CertificateController::class, 'issue'])
        ->name('artifacts.certificate.issue');
});

// Admin certificate management
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/certificates', [\App\Http\Controllers\Admin\CertificateController::class, 'index'])
            ->name('certificates.index');
        Route::post('/certificates/{certificate}/revoke', [\App\Http\Controllers\Admin\CertificateController::class, 'revoke'])
            ->name('certificates.revoke');
    });

/*
|--------------------------------------------------------------------------
| Internal API Routes — Met Museum & Geocoding
|--------------------------------------------------------------------------
| These are thin server-side proxies so the frontend never makes direct
| calls to external APIs (avoids CORS, enables server-side caching, and
| keeps all rate-limit logic in one place on the backend).
|
| Requires authentication — curators/collectors only.
*/
Route::middleware(['auth', 'verified'])
    ->prefix('api')
    ->name('api.')
    ->group(function () {
        // Metropolitan Museum of Art — free public API, no key required
        Route::get('/met/search',        [\App\Http\Controllers\Api\MetMuseumController::class, 'search'])->name('met.search');
        Route::get('/met/objects/{id}',  [\App\Http\Controllers\Api\MetMuseumController::class, 'show'])->name('met.show');

        // Nominatim (OpenStreetMap) geocoding — free, no key required
        Route::get('/geocode',           [\App\Http\Controllers\Api\GeocodingController::class, 'geocode'])->name('geocode');
        Route::get('/reverse-geocode',   [\App\Http\Controllers\Api\GeocodingController::class, 'reverse'])->name('reverse-geocode');
    });
