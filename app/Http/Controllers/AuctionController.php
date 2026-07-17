<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceAuctionBidRequest;
use App\Models\Auction;
use App\Models\ArtifactCategory;
use App\Services\AuctionBidService;
use App\Services\AuctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class AuctionController extends Controller
{
    public function __construct(
        private AuctionService    $auctionService,
        private AuctionBidService $bidService,
    ) {}

    /**
     * Public auction directory — browse all open and recently closed auctions.
     */
    public function index(Request $request): View
    {
        $query = Auction::with(['artifact.images', 'artifact.category', 'creator'])
            ->withCount('bids')
            ->whereIn('status', [Auction::STATUS_ACTIVE, Auction::STATUS_CLOSED]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        } else {
            // Default: open auctions first
            $query->orderByRaw("FIELD(status, 'active', 'closed')")->orderBy('ends_at');
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhereHas('artifact', fn ($a) => $a->where('name', 'like', "%{$search}%")));
        }

        $auctions = $query->paginate(12)->withQueryString();

        $activeCount = Auction::open()->count();

        return view('auctions.index', compact('auctions', 'activeCount'));
    }

    /**
     * Public auction detail with bid history and bid form.
     */
    public function show(Auction $auction): View
    {
        if ($auction->isDraft() || $auction->isCancelled()) {
            abort(404);
        }

        $this->auctionService->incrementViews($auction);

        $auction->load([
            'artifact.images',
            'artifact.category',
            'artifact.museum',
            'creator',
            'winner',
            'bids.bidder',
        ]);

        return view('auctions.show', compact('auction'));
    }

    /**
     * Place a bid on an active auction.
     */
    public function bid(PlaceAuctionBidRequest $request, Auction $auction): RedirectResponse
    {
        Gate::authorize('bid', $auction);

        try {
            $this->bidService->placeBid(
                $auction,
                $request->user(),
                (float) $request->validated('amount'),
            );

            return back()->with('success', 'Your bid of ' . $auction->currency . ' ' . number_format($request->validated('amount'), 2) . ' has been placed!');
        } catch (RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }
}
