<?php

namespace App\Http\Requests;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a submission from the public Contact page.
 *
 * Anyone — authenticated or not — is allowed to submit the contact form,
 * so authorize() simply returns true; abuse is mitigated at the route
 * level with throttle middleware rather than here.
 */
class SaveContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(ContactMessage::CATEGORIES))],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name.',
            'email.required' => 'An email address is required so we can reply to you.',
            'email.email' => 'Please enter a valid email address.',
            'category.in' => 'Please choose a valid inquiry type.',
            'message.required' => 'Please write a message before submitting.',
            'message.min' => 'Your message should be at least :min characters so we have enough context to help.',
        ];
    }
}
