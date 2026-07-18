<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuctionWatcherController extends Controller
{
    public function store(Request $request, Auction $auction): RedirectResponse
    {
        $request->user()->watchedAuctions()->syncWithoutDetaching([$auction->id]);
        return back()->with('success', 'You are now watching this auction.');
    }

    public function destroy(Request $request, Auction $auction): RedirectResponse
    {
        $request->user()->watchedAuctions()->detach($auction->id);
        return back()->with('success', 'You are no longer watching this auction.');
    }
}
