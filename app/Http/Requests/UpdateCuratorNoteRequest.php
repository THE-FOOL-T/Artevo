<?php

namespace App\Http\Requests;

use App\Models\CuratorNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCuratorNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'note_type' => ['required', Rule::in(array_keys(CuratorNote::TYPES))],
            'body'      => ['required', 'string', 'max:10000'],
            'is_pinned' => ['sometimes', 'boolean'],
        ];
    }
}
