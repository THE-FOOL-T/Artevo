@extends('layouts.app')

@section('title', 'Terms & Conditions — Artevo')

@section('content')
<section class="av-section">
    <div class="container max-w-content">
        <x-tag>Legal</x-tag>
        <h1 class="mt-4">Terms &amp; Conditions</h1>
        <p class="text-muted">Last updated: {{ now()->format('F j, Y') }}</p>

        <x-alert type="error" class="mb-8">
            This page is a starter template for development and demo purposes. Have it reviewed by a
            qualified attorney — particularly the auction and payments sections — before operating
            Artevo with real transactions.
        </x-alert>

        <h3>1. Acceptance of Terms</h3>
        <p>By creating an account or using Artevo, you agree to these Terms. If you do not agree, please
            do not use the platform.</p>

        <h3>2. Accounts</h3>
        <p>You are responsible for maintaining the confidentiality of your account credentials and for
            all activity that occurs under your account. Museums, curators, collectors and visitors each
            operate under role-based permissions described in our platform documentation.</p>

        <h3>3. Artifact Submissions</h3>
        <p>By uploading an artifact, you represent that you have the right to submit it, that the
            information provided is accurate to the best of your knowledge, and that you consent to
            curator review as part of the verification process.</p>

        <h3>4. Verification Is Not a Guarantee</h3>
        <p>A "Verified" status reflects a curator's review of the submitted metadata and documentation.
            It is an expert assessment, not a legal certification of authenticity, and Artevo makes no
            warranty as to an artifact's ultimate authenticity, condition, or market value.</p>

        <h3>5. Auctions &amp; Bidding</h3>
        <p>Placing a bid is a binding commitment to purchase the artifact at that price if you are the
            winning bidder and any applicable reserve price is met. Bids cannot be withdrawn once placed.
            Sellers are responsible for accurately describing the item being auctioned.</p>

        <h3>6. Prohibited Conduct</h3>
        <p>You may not use Artevo to list stolen or illegally excavated artifacts, submit fraudulent
            provenance documentation, manipulate auctions (including bidding on your own listings), or
            attempt to circumvent platform security.</p>

        <h3>7. Intellectual Property</h3>
        <p>You retain ownership of images and documents you upload, and grant Artevo a license to display
            them on the platform for the purpose of operating your listing, exhibition, or collection.</p>

        <h3>8. Disclaimers &amp; Limitation of Liability</h3>
        <p>Artevo is provided "as is." To the maximum extent permitted by law, Artevo is not liable for
            indirect or consequential damages arising from your use of the platform, including disputes
            between buyers and sellers in an auction.</p>

        <h3>9. Changes to These Terms</h3>
        <p>We may update these Terms from time to time. Continued use of Artevo after changes take effect
            constitutes acceptance of the revised Terms.</p>

        <h3>10. Contact</h3>
        <p>Questions about these Terms can be sent through our <a href="{{ route('contact') }}" class="text-accent">contact page</a>.</p>
    </div>
</section>
@endsection
