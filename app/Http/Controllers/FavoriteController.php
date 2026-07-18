<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Load relations for eager loading images etc.
        $artifacts = $user->favoritedArtifacts()
            ->with(['museum', 'images'])
            ->latest('artifact_favorites.created_at')
            ->get();
            
        $collections = $user->favoritedCollections()
            ->with('museum')
            ->latest('collection_favorites.created_at')
            ->get();
            
        $exhibitions = $user->favoritedExhibitions()
            ->with('museum')
            ->latest('exhibition_favorites.created_at')
            ->get();

        return view('favorites.index', compact('artifacts', 'collections', 'exhibitions'));
    }
}
