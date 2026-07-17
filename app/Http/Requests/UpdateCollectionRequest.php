<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('collection'));
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:180'],
            'description'         => ['nullable', 'string', 'max:5000'],
            'is_public'           => ['nullable', 'boolean'],
            'cover_image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_cover_image'  => ['nullable', 'boolean'],
        ];
    }

    /**
     * Coerce the checkbox boolean — same pattern as StoreCollectionRequest.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if (is_array($data)) {
            $data['is_public']           = (bool) ($data['is_public'] ?? true);
            $data['remove_cover_image']  = (bool) ($data['remove_cover_image'] ?? false);
        }

        return $data;
    }
}
