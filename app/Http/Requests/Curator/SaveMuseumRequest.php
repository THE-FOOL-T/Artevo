<?php

namespace App\Http\Requests\Curator;

use App\Models\Museum;
use Illuminate\Foundation\Http\FormRequest;

class SaveMuseumRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Museum::class);
        }

        return $this->user()->can('update', $this->route('museum'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'foundation_year' => ['nullable', 'integer', 'min:1000', 'max:' . now()->year],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'opening_hours' => ['nullable', 'array'],
            'opening_hours.*' => ['nullable', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'featured' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.max' => 'The logo must be smaller than 2MB.',
            'cover_image.max' => 'The cover image must be smaller than 4MB.',
        ];
    }
}
