<?php

namespace App\Services;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Handles everything that happens after a visitor submits the Contact

 */
class ContactService
{
    /**
     * Store a validated contact submission and queue the internal
     * notification email.
     *
     * @param  array<string, string>  $validated
     */
    public function submit(array $validated, Request $request): ContactMessage
    {
        $contactMessage = ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        // Queued so a slow SMTP connection never delays the visitor's
        // response — see QUEUE_CONNECTION in .env.
        Mail::to(config('artevo.contact_notify_email'))
            ->queue(new ContactMessageReceived($contactMessage));

        return $contactMessage;
    }
}
