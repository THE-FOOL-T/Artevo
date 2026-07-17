<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization via Gate::authorize() in controller
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'action'           => ['required', 'string', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
            'provenance_note'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required'                => 'Please choose approve or reject.',
            'action.in'                      => 'Action must be either approve or reject.',
            'rejection_reason.required_if'   => 'A rejection reason is required when rejecting a donation.',
        ];
    }
}
