<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Collection::class);
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:180'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'is_public'    => ['nullable', 'boolean'],
            'cover_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * Coerce the checkbox value — unchecked boxes send nothing, so we
     * default to true (public) when the field is absent.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if (is_array($data)) {
            $data['is_public'] = (bool) ($data['is_public'] ?? true);
        }

        return $data;
    }
}
