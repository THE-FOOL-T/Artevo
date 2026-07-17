<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\View\View;

/**
 * Public-facing collection browser.
 * Only public collections are ever surfaced here.
 */
class CollectionController extends Controller
{
    public function index(): View
    {
        $query = Collection::public()
            ->with(['museum', 'collector', 'artifacts'])
            ->withCount('artifacts');

        // Simple keyword search across name + description
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if (request()->boolean('featured')) {
            $query->featured();
        }

        $collections = $query->latest()->paginate(12)->withQueryString();

        $featuredCollections = Collection::public()
            ->featured()
            ->with(['museum', 'collector'])
            ->withCount('artifacts')
            ->latest()
            ->take(4)
            ->get();

        return view('collections.index', [
            'collections'         => $collections,
            'featuredCollections' => $featuredCollections,
        ]);
    }

    public function show(Collection $collection): View
    {
        // Guests can only see public collections
        if (! $collection->isPublic()) {
            abort_unless(auth()->check() && auth()->user()->can('view', $collection), 403);
        }

        $collection->incrementViews();

        $artifacts = $collection->artifacts()
            ->with(['category', 'images', 'museum', 'collector'])
            ->get();

        $favoritesCount  = $collection->favoritedBy()->count();
        $userHasFavorited = auth()->check()
            ? $collection->favoritedBy()->where('user_id', auth()->id())->exists()
            : false;

        // Related collections — same owner or overlapping artifacts
        $relatedCollections = Collection::public()
            ->where('id', '!=', $collection->id)
            ->where(function ($q) use ($collection) {
                $q->where('museum_id', $collection->museum_id)
                    ->orWhere('collector_id', $collection->collector_id);
            })
            ->withCount('artifacts')
            ->latest()
            ->take(3)
            ->get();

        return view('collections.show', [
            'collection'        => $collection,
            'artifacts'         => $artifacts,
            'favoritesCount'    => $favoritesCount,
            'userHasFavorited'  => $userHasFavorited,
            'relatedCollections' => $relatedCollections,
        ]);
    }
}
