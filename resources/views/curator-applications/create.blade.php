@extends('layouts.app')

@section('title', 'Apply for Curator Role — Artevo')

@section('content')
    <section class="av-section" style="padding-top: var(--space-10);">
        <div class="container" style="max-width: 600px;">
            <a href="{{ route('dashboard') }}" style="display:inline-block; margin-bottom:var(--space-6); color:var(--ink-500); font-size:var(--text-sm); font-weight:500;">
                &larr; Back to Dashboard
            </a>

            <div style="margin-bottom: var(--space-8);">
                <x-tag>Application</x-tag>
                <h1 style="margin-top: var(--space-4);">Apply to be a Curator</h1>
                <p>Curators can register museums, add verified artifacts, and curate collections. Please provide your institutional details below so our team can review your application.</p>
            </div>

            <form method="POST" action="{{ route('curator-applications.store') }}">
                @csrf

                <div class="av-field">
                    <label for="institution_name">Institution Name</label>
                    <input id="institution_name" name="institution_name" type="text" value="{{ old('institution_name') }}" required>
                    <span class="av-field__hint">The primary museum or organization you represent.</span>
                    @error('institution_name') <span class="av-field__error">{{ $message }}</span> @enderror
                </div>

                <div class="av-field">
                    <label for="job_title">Job Title / Role</label>
                    <input id="job_title" name="job_title" type="text" value="{{ old('job_title') }}" required>
                    @error('job_title') <span class="av-field__error">{{ $message }}</span> @enderror
                </div>

                <div class="av-field">
                    <label for="justification">Reason for Application</label>
                    <textarea id="justification" name="justification" required
                              style="width: 100%; min-height: 120px; padding: 0.6rem 0.8rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-family: inherit; font-size: inherit; resize: vertical;">{{ old('justification') }}</textarea>
                    <span class="av-field__hint">Briefly describe what you intend to curate on Artevo (e.g. adding the museum's Roman collection).</span>
                    @error('justification') <span class="av-field__error">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <x-button type="submit">Submit Application</x-button>
                </div>
            </form>
        </div>
    </section>
@endsection
