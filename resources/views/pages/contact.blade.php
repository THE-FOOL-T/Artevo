@extends('layouts.app')

@section('title', 'Contact Artevo')
@section('meta_description', 'Reach the Artevo team for museum partnerships, account support, bug reports or feature suggestions.')

@section('content')

    <section class="av-section" style="padding-top: var(--space-12); padding-bottom: var(--space-8);">
        <div class="container">
            <x-tag>Contact</x-tag>
            <h1 style="margin-top: var(--space-4);">Talk to <em>the Artevo team</em></h1>
            <p style="max-width: 560px;">
                Whether you're a museum exploring a partnership, a collector with a question about verification,
                or you've spotted something broken — tell us below.
            </p>
        </div>
    </section>

    <section class="av-section">
        <div class="container">
            <div class="grid grid-2" style="gap: var(--space-10); align-items: start;">

                <div data-reveal>
                    @if (session('success'))
                        <x-alert type="success">{{ session('success') }}</x-alert>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" novalidate>
                        @csrf

                        <div class="av-field">
                            <label for="name">Full name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                            @error('name') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>

                        <div class="av-field">
                            <label for="email">Email address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>

                        <div class="av-field">
                            <label for="category">What's this about?</label>
                            <select id="category" name="category" required>
                                @foreach ($categories as $value => $label)
                                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>

                        <div class="av-field">
                            <label for="subject">Subject <span class="text-muted">(optional)</span></label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}">
                            @error('subject') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>

                        <div class="av-field">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required minlength="10">{{ old('message') }}</textarea>
                            <span class="av-field__hint">At least 10 characters — the more detail, the faster we can help.</span>
                            @error('message') <span class="av-field__error">{{ $message }}</span> @enderror
                        </div>

                        <x-button type="submit" variant="primary" block>Send message</x-button>
                    </form>
                </div>

                <div>
                    <x-card eyebrow="Response time" data-reveal data-reveal-delay="1">
                        <h3>We typically reply within 1–2 business days</h3>
                        <p>Museum partnership inquiries and account support are prioritized first.</p>
                    </x-card>
                    <x-card eyebrow="Before you write in" data-reveal data-reveal-delay="2" class="mt-6">
                        <h3>Bug reports</h3>
                        <p>Include the page URL and what you expected to happen versus what actually happened —
                            it helps us fix it faster.</p>
                    </x-card>
                </div>
            </div>
        </div>
    </section>

@endsection
