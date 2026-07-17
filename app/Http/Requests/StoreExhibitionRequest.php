<?php

namespace App\Http\Requests;

use App\Models\Exhibition;
use Illuminate\Foundation\Http\FormRequest;

class StoreExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Exhibition::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:180'],
            'tagline'       => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'status'        => ['nullable', 'in:' . implode(',', Exhibition::STATUSES)],
            'starts_at'     => ['nullable', 'date'],
            'ends_at'       => ['nullable', 'date', 'after_or_equal:starts_at'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],
            'location'      => ['nullable', 'string', 'max:255'],
            'cover_image'   => ['nullable', 'image', 'max:5120'],
            'is_featured'   => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
