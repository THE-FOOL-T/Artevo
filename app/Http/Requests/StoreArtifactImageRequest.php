<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtifactImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMedia', $this->route('artifact'));
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'caption' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => 'Upload up to 10 images at a time.',
            'images.*.max' => 'Each image must be smaller than 6MB.',
        ];
    }
}
