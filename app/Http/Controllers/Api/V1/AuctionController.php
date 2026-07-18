<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $auctions = Auction::where('status', 'active')->paginate(15);

        return AuctionResource::collection($auctions);
    }

    public function show(Auction $auction)
    {
        if ($auction->status !== 'active') {
            abort(404);
        }

        return new AuctionResource($auction);
    }
}
