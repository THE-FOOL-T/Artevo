<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuctionRequest;
use App\Models\Artifact;
use App\Models\Auction;
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
     * "List artifact for auction" form — collector variant.
     */
    public function create(Artifact $artifact): View
    {
        Gate::authorize('create', [Auction::class, $artifact]);

        // Reuse the curator view (same form, no museum context)
        return view('curator.auctions.create', ['museum' => null, 'artifact' => $artifact]);
    }

    /**
     * Save draft auction for a collector-owned artifact.
     */
    public function store(StoreAuctionRequest $request, Artifact $artifact): RedirectResponse
    {
        Gate::authorize('create', [Auction::class, $artifact]);

        $data          = $request->validated();
        $data['title'] = $data['title'] ?: $artifact->name;

        $this->auctionService->create($artifact, $request->user(), $data);

        return redirect()
            ->route('collector.artifacts.edit', $artifact)
            ->with('success', 'Auction draft created. Publish it when you\'re ready.');
    }

    public function publish(Auction $auction): RedirectResponse
    {
        Gate::authorize('publish', $auction);
        $this->auctionService->publish($auction);

        return back()->with('success', 'Auction is now live and accepting bids.');
    }

    public function close(Auction $auction): RedirectResponse
    {
        Gate::authorize('close', $auction);
        $this->auctionService->close($auction);

        return back()->with('success', 'Auction closed and winner notified.');
    }

    public function cancel(Auction $auction): RedirectResponse
    {
        Gate::authorize('cancel', $auction);
        $this->auctionService->cancel($auction);

        return back()->with('success', 'Auction cancelled.');
    }
}
