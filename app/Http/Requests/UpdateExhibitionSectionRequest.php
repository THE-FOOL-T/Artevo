<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExhibitionSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled via Gate::authorize('update', $section->exhibition) in controller
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'body'  => ['nullable', 'string'],
        ];
    }
}
