<?php

namespace App\Http\Requests;

use App\Models\Exhibition;
use Illuminate\Foundation\Http\FormRequest;

class SaveExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Exhibition::class);
        }
        return $this->user()->can('update', $this->route('exhibition'));
    }

    public function rules(): array
    {
        $rules = [
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

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['remove_cover_image']  = ['nullable', 'boolean'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $mergeData = [
            'is_featured' => $this->boolean('is_featured'),
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $mergeData['remove_cover_image'] = $this->boolean('remove_cover_image');
        }

        $this->merge($mergeData);
    }
}
