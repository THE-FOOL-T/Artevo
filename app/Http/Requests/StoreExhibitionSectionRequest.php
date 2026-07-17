<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExhibitionSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled upstream in ExhibitionSectionController
        // via Gate::authorize('update', $exhibition)
        return true;
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
