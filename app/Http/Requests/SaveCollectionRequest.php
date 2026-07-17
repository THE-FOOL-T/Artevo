<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;

class SaveCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Collection::class);
        }
        
        return $this->user()->can('update', $this->route('collection'));
    }

    public function rules(): array
    {
        $rules = [
            'name'         => ['required', 'string', 'max:180'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'is_public'    => ['nullable', 'boolean'],
            'cover_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['remove_cover_image']  = ['nullable', 'boolean'];
        }

        return $rules;
    }

    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if (is_array($data)) {
            $data['is_public'] = (bool) ($data['is_public'] ?? true);
            
            if ($this->isMethod('put') || $this->isMethod('patch')) {
                $data['remove_cover_image'] = (bool) ($data['remove_cover_image'] ?? false);
            }
        }

        return $data;
    }
}
