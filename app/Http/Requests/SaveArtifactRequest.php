<?php

namespace App\Http\Requests;

use App\Models\Artifact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveArtifactRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Artifact::class);
        }

        return $this->user()->can('update', $this->route('artifact'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:180'],
            'category_id' => ['required', 'exists:artifact_categories,id'],
            'material_id' => ['nullable', 'exists:artifact_materials,id'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'civilization' => ['nullable', 'string', 'max:120'],
            'era' => ['nullable', 'string', 'max:120'],
            'century' => ['nullable', 'string', 'max:60'],
            'country_of_origin' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'discovery_location' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:80'],
            'dimensions' => ['nullable', 'string', 'max:120'],
            'weight' => ['nullable', 'string', 'max:60'],
            'condition' => ['nullable', 'string', 'max:40'],
            'estimated_value' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'status' => ['required', Rule::in(Artifact::STATUSES)],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
