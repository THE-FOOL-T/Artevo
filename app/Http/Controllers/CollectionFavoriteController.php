<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Handles toggling a user's favorite on a collection.
 * Works for both redirect-based flows and AJAX calls.
 */
class CollectionFavoriteController extends Controller
{
    public function __construct(private CollectionService $collectionService) {}

    /**
     * Favorite a collection.
     */
    public function store(Collection $collection): JsonResponse|RedirectResponse
    {
        Gate::authorize('favorite', $collection);

        $this->collectionService->toggleFavorite(auth()->user(), $collection);

        if (request()->expectsJson()) {
            return response()->json([
                'favorited' => true,
                'count'     => $collection->favoritedBy()->count(),
            ]);
        }

        return back()->with('success', 'Added to your favorites.');
    }

    /**
     * Unfavorite a collection.
     */
    public function destroy(Collection $collection): JsonResponse|RedirectResponse
    {
        Gate::authorize('favorite', $collection);

        $this->collectionService->toggleFavorite(auth()->user(), $collection);

        if (request()->expectsJson()) {
            return response()->json([
                'favorited' => false,
                'count'     => $collection->favoritedBy()->count(),
            ]);
        }

        return back()->with('success', 'Removed from your favorites.');
    }
}
