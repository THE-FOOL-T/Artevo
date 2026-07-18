@extends('layouts.app')

@section('title', 'Analytics Dashboard — Admin')

@section('content')

    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container max-w-content">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: var(--space-8);">
                <div>
                    <x-tag>Administration</x-tag>
                    <h1 style="margin-top: var(--space-4);">Analytics Dashboard</h1>
                    <p style="color: var(--text-muted); margin-top: var(--space-2);">Platform metrics and growth overview.</p>
                </div>
                @can('export-reports')
                <div style="display: flex; gap: var(--space-4);">
                    <x-button component="a" href="{{ route('admin.reports.index') }}" variant="outline">
                        Data Exports
                    </x-button>
                </div>
                @endcan
            </div>

            <div class="grid grid-3" style="gap: var(--space-6); margin-bottom: var(--space-8);">
                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Total Users</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['total_users']) }}</div>
                    <div style="font-size: var(--text-sm); color: var(--color-green-600); margin-top: var(--space-2);">
                        +{{ $newUsersThisMonth }} this month
                    </div>
                </x-card>

                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Total Artifacts</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['total_artifacts']) }}</div>
                    <div style="font-size: var(--text-sm); color: var(--color-green-600); margin-top: var(--space-2);">
                        +{{ $newArtifactsThisMonth }} this month
                    </div>
                </x-card>

                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Verified Museums</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['verified_museums']) }}</div>
                    <div style="font-size: var(--text-sm); color: var(--text-muted); margin-top: var(--space-2);">
                        Out of {{ number_format($metrics['total_museums']) }} total
                    </div>
                </x-card>

                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Public Artifacts</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['public_artifacts']) }}</div>
                </x-card>

                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Active Auctions</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['active_auctions']) }}</div>
                </x-card>

                <x-card>
                    <div style="font-size: var(--text-sm); font-weight: 500; color: var(--text-muted);">Pending Donations</div>
                    <div style="font-size: var(--text-3xl); font-weight: 700; margin-top: var(--space-2);">{{ number_format($metrics['pending_donations']) }}</div>
                </x-card>
            </div>
        </div>
    </section>

@endsection
