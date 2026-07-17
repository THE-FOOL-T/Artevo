<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled via Gate::authorize() in the controller
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'museum_id' => ['required', 'integer', 'exists:museums,id'],
            'message'   => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'museum_id.required' => 'Please select a museum to donate to.',
            'museum_id.exists'   => 'The selected museum does not exist.',
        ];
    }
}
