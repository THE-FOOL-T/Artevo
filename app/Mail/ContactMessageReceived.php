<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the Artevo support inbox of a new contact form submission.

 */
class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('New Artevo inquiry: ' . ($this->contactMessage->subject ?: ucfirst(str_replace('_', ' ', $this->contactMessage->category))))
            ->view('emails.contact-message');
    }
}
