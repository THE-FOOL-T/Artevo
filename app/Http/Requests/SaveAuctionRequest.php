<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate::authorize('create', [$auction, $artifact]) in controller
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'         => ['nullable', 'string', 'max:220'],
            'description'   => ['nullable', 'string'],
            'reserve_price' => ['required', 'numeric', 'min:1'],
            'bid_increment' => ['required', 'numeric', 'min:0.01'],
            'starts_at'     => ['required', 'date', 'after_or_equal:now'],
            'ends_at'       => ['required', 'date', 'after:starts_at'],
            'currency'      => ['nullable', 'string', 'size:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after'       => 'The closing time must be after the start time.',
            'starts_at.after_or_equal' => 'The auction cannot start in the past.',
        ];
    }
}
