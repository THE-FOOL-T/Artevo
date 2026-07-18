@extends('layouts.app')

@section('title', 'Data Exports — Admin')

@section('content')

    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: var(--space-8);">
                <div>
                    <x-tag>Administration</x-tag>
                    <h1 style="margin-top: var(--space-4);">Data Exports</h1>
                    <p style="color: var(--text-muted); margin-top: var(--space-2);">Download raw data reports as CSV files.</p>
                </div>
                <div style="display: flex; gap: var(--space-4);">
                    <x-button component="a" href="{{ route('admin.analytics.index') }}" variant="outline">
                        Back to Analytics
                    </x-button>
                </div>
            </div>

            <div class="grid grid-3" style="gap: var(--space-6);">
                <x-card>
                    <h3 style="font-size: var(--text-lg); font-weight: 500; margin-bottom: var(--space-2);">Users Report</h3>
                    <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Export all registered users on the platform, including their current roles and join dates.</p>
                    <x-button component="a" href="{{ route('admin.reports.export', 'users') }}" variant="primary">Download CSV</x-button>
                </x-card>

                <x-card>
                    <h3 style="font-size: var(--text-lg); font-weight: 500; margin-bottom: var(--space-2);">Museums Report</h3>
                    <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Export all museums, showing curator information, location details, and verification status.</p>
                    <x-button component="a" href="{{ route('admin.reports.export', 'museums') }}" variant="primary">Download CSV</x-button>
                </x-card>

                <x-card>
                    <h3 style="font-size: var(--text-lg); font-weight: 500; margin-bottom: var(--space-2);">Artifacts Report</h3>
                    <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Export all artifacts across all museums and collections, including verification status.</p>
                    <x-button component="a" href="{{ route('admin.reports.export', 'artifacts') }}" variant="primary">Download CSV</x-button>
                </x-card>
            </div>
        </div>
    </section>

@endsection
