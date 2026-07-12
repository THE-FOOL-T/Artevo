<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1E1B16; background: #F8F5EF; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #E7E1D3; border-radius: 12px; padding: 32px;">
        <p style="font-family: monospace; color: #8A6A22; letter-spacing: 0.08em; text-transform: uppercase; font-size: 12px;">Artevo &middot; Contact Form</p>
        <h2 style="margin-top: 8px;">{{ $contactMessage->subject ?: ucfirst(str_replace('_', ' ', $contactMessage->category)) }}</h2>

        <p><strong>From:</strong> {{ $contactMessage->name }} &lt;{{ $contactMessage->email }}&gt;</p>
        <p><strong>Category:</strong> {{ \App\Models\ContactMessage::CATEGORIES[$contactMessage->category] ?? $contactMessage->category }}</p>

        <div style="margin-top: 16px; padding: 16px; background: #F1E4C4; border-radius: 8px; white-space: pre-line;">
            {{ $contactMessage->message }}
        </div>

        <p style="margin-top: 24px; font-size: 12px; color: #8C8474;">
            Submitted {{ $contactMessage->created_at->format('M j, Y \a\t g:i A') }} from IP {{ $contactMessage->ip_address }}
        </p>
    </div>
</body>
</html>
