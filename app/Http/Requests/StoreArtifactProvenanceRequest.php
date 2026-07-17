<?php

namespace App\Http\Requests;

use App\Models\ArtifactProvenance;
use Illuminate\Foundation\Http\FormRequest;

class StoreArtifactProvenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled in controller via Gate::authorize('manageProvenance', $artifact)
        return true;
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
