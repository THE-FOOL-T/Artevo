<?php

namespace App\Http\Requests;

use App\Models\RestorationRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestorationRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'category'             => ['required', 'string', 'max:60'],
            'title'                => ['required', 'string', 'max:180'],
            'description'          => ['nullable', 'string'],
            'conservator_name'     => ['nullable', 'string', 'max:120'],
            'institution'          => ['nullable', 'string', 'max:180'],
            'started_at'           => ['nullable', 'date'],
            'completed_at'         => ['nullable', 'date', 'after_or_equal:started_at'],
            'artifact_document_id' => ['nullable', 'integer', 'exists:artifact_documents,id'],
        ];
    }
}
