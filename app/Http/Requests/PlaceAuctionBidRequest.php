<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceAuctionBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization checked in controller via Gate::authorize('bid', $auction)
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
