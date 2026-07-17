<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArtifactProvenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled via Gate::authorize('manageProvenance', $artifact) in controller
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'        => ['required', 'string', 'max:60'],
            'title'       => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'date'        => ['nullable', 'date'],
            'location'    => ['nullable', 'string', 'max:180'],
            'source_url'  => ['nullable', 'url', 'max:500'],
        ];
    }
}
