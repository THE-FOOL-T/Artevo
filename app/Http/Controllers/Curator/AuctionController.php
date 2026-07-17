<?php

namespace App\Http\Controllers\Curator;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAuctionRequest;
use App\Models\Artifact;
use App\Models\Auction;
use App\Models\Museum;
use App\Services\AuctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuctionController extends Controller
{
    public function __construct(
        private AuctionService $auctionService,
    ) {}

    /**
     * Show the "List artifact for auction" form.
     */
    public function create(Museum $museum, Artifact $artifact): View
    {
        Gate::authorize('create', [Auction::class, $artifact]);

        return view('curator.auctions.create', compact('museum', 'artifact'));
    }

    /**
     * Save a draft auction.
     */
    public function store(SaveAuctionRequest $request, Museum $museum, Artifact $artifact): RedirectResponse
    {
        Gate::authorize('create', [Auction::class, $artifact]);

        $data         = $request->validated();
        $data['title'] = $data['title'] ?: $artifact->name;

        $auction = $this->auctionService->create($artifact, $request->user(), $data);

        return redirect()
            ->route('curator.museums.artifacts.edit', [$museum, $artifact])
            ->with('success', 'Auction draft created. Publish it when you\'re ready.');
    }

    /**
     * Transition draft → active.
     */
    public function publish(Auction $auction): RedirectResponse
    {
        Gate::authorize('publish', $auction);

        $this->auctionService->publish($auction);

        return back()->with('success', 'Auction is now live and accepting bids.');
    }

    /**
     * Close an active auction early.
     */
    public function close(Auction $auction): RedirectResponse
    {
        Gate::authorize('close', $auction);

        $this->auctionService->close($auction);

        return back()->with('success', 'Auction closed and winner notified.');
    }

    /**
     * Cancel a draft or bid-free active auction.
     */
    public function cancel(Auction $auction): RedirectResponse
    {
        Gate::authorize('cancel', $auction);

        $this->auctionService->cancel($auction);

        return back()->with('success', 'Auction cancelled.');
    }
}
