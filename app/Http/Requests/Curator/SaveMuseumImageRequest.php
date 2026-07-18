<?php

namespace App\Http\Requests\Curator;

use Illuminate\Foundation\Http\FormRequest;

class SaveMuseumImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMedia', $this->route('museum'));
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => 'Upload up to 10 images at a time.',
            'images.*.max' => 'Each image must be smaller than 4MB.',
        ];
    }
}
