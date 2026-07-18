<?php

use App\Http\Controllers\Api\V1\ArtifactController;
use App\Http\Controllers\Api\V1\AuctionController;
use App\Http\Controllers\Api\V1\ExhibitionController;
use App\Http\Controllers\Api\V1\MuseumController;
use App\Http\Controllers\Api\V1\CollectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Endpoints
    Route::get('/museums', [MuseumController::class, 'index']);
    Route::get('/museums/{museum:slug}', [MuseumController::class, 'show']);

    Route::get('/artifacts', [ArtifactController::class, 'index']);
    Route::get('/artifacts/{artifact:slug}', [ArtifactController::class, 'show']);

    Route::get('/exhibitions', [ExhibitionController::class, 'index']);
    Route::get('/exhibitions/{exhibition:slug}', [ExhibitionController::class, 'show']);

    Route::get('/auctions', [AuctionController::class, 'index']);
    Route::get('/auctions/{auction}', [AuctionController::class, 'show']);

    // Protected Endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/collections', [CollectionController::class, 'index']);
    });
});
