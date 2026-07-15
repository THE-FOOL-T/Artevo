@extends('layouts.app')

@section('title', 'Your Profile — Artevo')

@section('content')

    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <x-tag>Account</x-tag>
            <h1 style="margin-top: var(--space-4);">Your profile</h1>

            <div style="margin-top: var(--space-8);">
                <x-card>
                    @include('profile.partials.update-profile-information-form')
                </x-card>

                <x-card class="mt-6">
                    @include('profile.partials.update-password-form')
                </x-card>

                <x-card class="mt-6">
                    @include('profile.partials.delete-account-form')
                </x-card>
            </div>
        </div>
    </section>

@endsection
