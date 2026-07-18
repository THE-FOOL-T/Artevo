<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExhibitionFavoriteController extends Controller
{
    public function store(Request $request, Exhibition $exhibition): RedirectResponse
    {
        $request->user()->favoritedExhibitions()->syncWithoutDetaching([$exhibition->id]);
        return back()->with('success', 'Exhibition added to your favorites.');
    }

    public function destroy(Request $request, Exhibition $exhibition): RedirectResponse
    {
        $request->user()->favoritedExhibitions()->detach($exhibition->id);
        return back()->with('success', 'Exhibition removed from your favorites.');
    }
}
