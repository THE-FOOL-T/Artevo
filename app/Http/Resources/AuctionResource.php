<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'reserve_price' => $this->reserve_price,
            'current_price' => $this->current_price,
            'bid_increment' => $this->bid_increment,
            'currency' => $this->currency,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'bids_count' => $this->bids_count,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
