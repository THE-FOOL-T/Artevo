@extends('layouts.app')

@section('title', 'Privacy Policy — Artevo')

@section('content')
<section class="av-section">
    <div class="container max-w-content">
        <x-tag>Legal</x-tag>
        <h1 class="mt-4">Privacy Policy</h1>
        <p class="text-muted">Last updated: {{ now()->format('F j, Y') }}</p>

        <x-alert type="error" class="mb-8">
            This page is a starter template for development and demo purposes. Have it reviewed by a
            qualified attorney before using Artevo with real user data.
        </x-alert>

        <h3>1. Information We Collect</h3>
        <p>When you create an account, upload an artifact, place a bid, or contact us, we collect the
            information you provide directly — such as your name, email address, uploaded images and
            documents, and the content of your messages. We also automatically collect technical
            information such as your IP address, browser type, and pages visited, to keep the platform
            secure and to improve it.</p>

        <h3>2. How We Use Your Information</h3>
        <p>We use collected information to operate your account, process verification requests and
            auction bids, send transactional notifications (such as outbid alerts or verification
            results), respond to support requests, and maintain the security and integrity of the
            platform.</p>

        <h3>3. Cookies &amp; Sessions</h3>
        <p>Artevo uses session cookies required for authentication and CSRF protection. These are
            essential to the platform functioning and cannot be disabled while remaining signed in.</p>

        <h3>4. Sharing of Information</h3>
        <p>We do not sell personal information. Limited information may be shared with partner museums
            when you submit an artifact to them, with payment processors to complete an auction
            transaction, or when required by law.</p>

        <h3>5. Data Retention</h3>
        <p>Account and artifact records are retained for as long as your account is active. Provenance
            and ownership history records are retained indefinitely once published, as they form part of
            an artifact's permanent historical record.</p>

        <h3>6. Your Rights</h3>
        <p>You may request a copy of the personal data associated with your account, request corrections,
            or request account deletion, subject to our obligation to preserve historical provenance
            records tied to artifacts you have owned or verified.</p>

        <h3>7. Children's Privacy</h3>
        <p>Artevo is not directed at children under 16, and we do not knowingly collect personal
            information from them.</p>

        <h3>8. Changes to This Policy</h3>
        <p>We may update this policy as the platform evolves. Material changes will be announced on this
            page with an updated "Last updated" date.</p>

        <h3>9. Contact</h3>
        <p>Questions about this policy can be sent through our <a href="{{ route('contact') }}" class="text-accent">contact page</a>.</p>
    </div>
</section>
@endsection
