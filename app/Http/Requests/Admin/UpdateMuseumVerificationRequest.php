<?php

namespace App\Http\Requests\Admin;

use App\Models\Museum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMuseumVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('verify', Museum::class);
    }

    public function rules(): array
    {
        return [
            'verification_status' => ['required', 'string', Rule::in([
                Museum::VERIFICATION_PENDING,
                Museum::VERIFICATION_VERIFIED,
                Museum::VERIFICATION_REJECTED,
            ])],
        ];
    }
}
