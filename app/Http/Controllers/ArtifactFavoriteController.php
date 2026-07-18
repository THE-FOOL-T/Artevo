<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArtifactFavoriteController extends Controller
{
    public function store(Request $request, Artifact $artifact): RedirectResponse
    {
        $request->user()->favoritedArtifacts()->syncWithoutDetaching([$artifact->id]);
        return back()->with('success', 'Artifact added to your favorites.');
    }

    public function destroy(Request $request, Artifact $artifact): RedirectResponse
    {
        $request->user()->favoritedArtifacts()->detach($artifact->id);
        return back()->with('success', 'Artifact removed from your favorites.');
    }
}
