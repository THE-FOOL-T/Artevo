<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveArtifactDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageMedia', $this->route('artifact'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'document_type' => ['nullable', 'string', 'max:60'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'document.mimes' => 'Documents must be a PDF, JPG, or PNG file.',
            'document.max' => 'The document must be smaller than 10MB.',
        ];
    }
}
