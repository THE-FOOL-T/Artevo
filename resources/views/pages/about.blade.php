@extends('layouts.app')

@section('title', 'About Artevo — Our Mission')
@section('meta_description', 'Artevo exists to give every historical artifact a verifiable digital record — learn about our mission and how the platform works.')

@section('content')

    <section class="av-section" style="padding-top: var(--space-12); padding-bottom: var(--space-10);">
        <div class="container">
            <x-tag>About Artevo</x-tag>
            <h1 style="margin-top: var(--space-4); max-width: 720px;">
                A permanent, verifiable record for the objects that carry <em>history</em> forward.
            </h1>
            <p style="font-size: var(--text-lg); max-width: 640px;">
                Artifacts move between collectors, museums and auction houses constantly — and paperwork gets
                lost. Artevo gives every piece a single, durable digital record of what it is, where it came
                from, and who has verified it.
            </p>
        </div>
    </section>

    <section class="av-section av-section--white">
        <div class="container">
            <x-section-heading eyebrow="Why It Matters">
                <h2>What we're building toward</h2>
            </x-section-heading>

            <div class="grid grid-2">
                <x-card eyebrow="Preservation" data-reveal>
                    <h3>Nothing gets forgotten</h3>
                    <p>Every artifact's images, documents, dimensions and history live in one structured record —
                        not scattered across spreadsheets, folders and paper files.</p>
                </x-card>
                <x-card eyebrow="Trust" data-reveal data-reveal-delay="1">
                    <h3>Verification you can check</h3>
                    <p>Curators review submissions before an artifact is marked verified, and the review outcome
                        is always visible on the object's own page.</p>
                </x-card>
                <x-card eyebrow="Transparency" data-reveal data-reveal-delay="2">
                    <h3>Provenance that can't be quietly edited</h3>
                    <p>Ownership history is append-only — new records are added, but nothing already on the
                        record is ever silently overwritten.</p>
                </x-card>
                <x-card eyebrow="Access" data-reveal data-reveal-delay="3">
                    <h3>Open to the public, useful to specialists</h3>
                    <p>Anyone can browse verified exhibitions and artifact pages; museums, curators and
                        researchers get the deeper tools they need underneath.</p>
                </x-card>
            </div>
        </div>
    </section>

    <section class="av-section">
        <div class="container">
            <x-section-heading eyebrow="Who Uses Artevo">
                <h2><em>Four roles</em>, one shared record</h2>
            </x-section-heading>

            <div class="grid grid-2">
                <x-card data-reveal>
                    <h3>Museums &amp; Curators</h3>
                    <p>Manage a museum profile and its full collection, run exhibitions, and lead the
                        verification workflow for every submitted artifact.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="1">
                    <h3>Collectors</h3>
                    <p>Archive a private collection, request verification, list an artifact for auction, or
                        donate a piece to a partner museum.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="2">
                    <h3>Researchers</h3>
                    <p>Trace an artifact's provenance, read curator notes and follow its restoration and
                        exhibition history over time.</p>
                </x-card>
                <x-card data-reveal data-reveal-delay="3">
                    <h3>Visitors</h3>
                    <p>Explore public exhibitions and the artifact gallery, scan an object's QR code on-site,
                        and follow auctions as they happen.</p>
                </x-card>
            </div>
        </div>
    </section>

    <section class="av-section text-center">
        <div class="container">
            <h2>Questions about how it works?</h2>
            <p class="max-w-content">We're happy to walk through the verification process, auction rules, or
                anything else before you get started.</p>
            <x-button href="{{ route('contact') }}" variant="primary">Contact us</x-button>
        </div>
    </section>

@endsection
