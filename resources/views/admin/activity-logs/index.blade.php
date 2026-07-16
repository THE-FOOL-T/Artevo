@extends('layouts.app')

@section('title', 'Activity Log — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>Admin</x-tag>
            <h1 style="margin-top: var(--space-4);">Activity log</h1>
            <p style="max-width: 560px;">{{ $logs->total() }} recorded actions. Every login, registration,
                profile change, and role change is captured here — later phases add artifact, auction and
                verification events to the same feed.</p>

            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex gap-3" style="margin-top: var(--space-6); flex-wrap: wrap; align-items: flex-end;">
                <div class="av-field" style="margin-bottom: 0; min-width: 200px;">
                    <label for="action">Action</label>
                    <select name="action" id="action">
                        <option value="">All actions</option>
                        @foreach ($actions as $actionOption)
                            <option value="{{ $actionOption }}" @selected(request('action') === $actionOption)>{{ $actionOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="av-field" style="margin-bottom: 0;">
                    <label for="from">From</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}">
                </div>
                <div class="av-field" style="margin-bottom: 0;">
                    <label for="to">To</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}">
                </div>
                <x-button type="submit" variant="primary">Filter</x-button>
                @if (request()->hasAny(['action', 'from', 'to', 'user_id']))
                    <x-button href="{{ route('admin.activity-logs.index') }}" variant="outline-dark">Clear</x-button>
                @endif
            </form>

            <div style="overflow-x: auto; margin-top: var(--space-6); border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <table style="width: 100%; border-collapse: collapse; font-size: var(--text-sm);">
                    <thead>
                        <tr style="background: var(--porcelain-100); text-align: left;">
                            <th style="padding: var(--space-3) var(--space-4);">When</th>
                            <th style="padding: var(--space-3) var(--space-4);">User</th>
                            <th style="padding: var(--space-3) var(--space-4);">Action</th>
                            <th style="padding: var(--space-3) var(--space-4);">Description</th>
                            <th style="padding: var(--space-3) var(--space-4);">IP address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr style="border-top: 1px solid var(--color-border);">
                                <td style="padding: var(--space-3) var(--space-4); white-space: nowrap; color: var(--ink-700);">{{ $log->created_at->format('M j, g:i A') }}</td>
                                <td style="padding: var(--space-3) var(--space-4);">{{ $log->user?->name ?? 'Deleted user' }}</td>
                                <td style="padding: var(--space-3) var(--space-4);"><x-tag class="av-tag--pill">{{ $log->action }}</x-tag></td>
                                <td style="padding: var(--space-3) var(--space-4);">{{ $log->description }}</td>
                                <td style="padding: var(--space-3) var(--space-4); color: var(--stone-600);">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: var(--space-6); text-align: center; color: var(--ink-700);">No activity matches these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: var(--space-6);">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
@endsection
