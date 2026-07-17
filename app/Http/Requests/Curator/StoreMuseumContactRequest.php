<?php

namespace App\Http\Requests\Curator;

use Illuminate\Foundation\Http\FormRequest;

class StoreMuseumContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMedia', $this->route('museum'));
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * At least one of email/phone must be given — a contact entry with
     * neither is useless.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('email') && ! $this->filled('phone')) {
                $validator->errors()->add('email', 'Provide at least an email or a phone number.');
            }
        });
    }
}
