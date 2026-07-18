@extends('layouts.app')

@section('title', 'Curator Applications — Admin Dashboard')

@section('content')
    <section class="av-section" style="padding-top: var(--space-10);">
        <div class="container">
            <a href="{{ route('dashboard') }}" style="display:inline-block; margin-bottom:var(--space-6); color:var(--ink-500); font-size:var(--text-sm); font-weight:500;">
                &larr; Back to Admin Dashboard
            </a>

            <div style="margin-bottom: var(--space-8);">
                <x-tag>Admin</x-tag>
                <h1 style="margin-top: var(--space-4);">Curator Applications</h1>
                <p>Review and approve requests from users to become Curators.</p>
            </div>

            @if ($applications->isEmpty())
                <div style="padding: var(--space-12) 0; text-align: center; color: var(--ink-500);">
                    <p>No applications found.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: var(--text-sm);">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Applicant</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Institution</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Job Title</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600; width: 30%;">Justification</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Status</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr style="border-bottom: 1px solid var(--color-border); {{ $app->isPending() ? 'background: rgba(245, 158, 11, 0.05);' : '' }}">
                                    <td style="padding: var(--space-4);">
                                        <div style="font-weight: 500;">{{ $app->applicant->name }}</div>
                                        <div style="color: var(--ink-500); font-size: var(--text-xs);">{{ $app->applicant->email }}</div>
                                    </td>
                                    <td style="padding: var(--space-4);">{{ $app->institution_name }}</td>
                                    <td style="padding: var(--space-4);">{{ $app->job_title }}</td>
                                    <td style="padding: var(--space-4); color: var(--ink-600);">
                                        <div style="max-height: 80px; overflow-y: auto;">
                                            {{ $app->justification }}
                                        </div>
                                    </td>
                                    <td style="padding: var(--space-4);">
                                        @if ($app->isPending())
                                            <span style="display:inline-block; padding:0.2rem 0.5rem; background:rgba(245,158,11,0.1); color:#d97706; border-radius:var(--radius-sm); font-size:var(--text-xs); font-weight:600;">Pending</span>
                                        @elseif ($app->isApproved())
                                            <span style="display:inline-block; padding:0.2rem 0.5rem; background:rgba(16,185,129,0.1); color:#059669; border-radius:var(--radius-sm); font-size:var(--text-xs); font-weight:600;">Approved</span>
                                        @else
                                            <span style="display:inline-block; padding:0.2rem 0.5rem; background:rgba(239,68,68,0.1); color:#dc2626; border-radius:var(--radius-sm); font-size:var(--text-xs); font-weight:600;">Rejected</span>
                                        @endif
                                    </td>
                                    <td style="padding: var(--space-4); text-align: right;">
                                        @if ($app->isPending())
                                            <form method="POST" action="{{ route('admin.curator-applications.update', $app) }}" style="display:inline-block; margin-right: 4px;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" style="padding:0.4rem 0.8rem; background:var(--ink-900); color:#fff; border:none; border-radius:var(--radius-sm); font-size:var(--text-xs); cursor:pointer;">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.curator-applications.update', $app) }}" style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" style="padding:0.4rem 0.8rem; background:transparent; color:var(--red-600); border:1px solid var(--color-border); border-radius:var(--radius-sm); font-size:var(--text-xs); cursor:pointer;">Reject</button>
                                            </form>
                                        @else
                                            <span style="color: var(--ink-500); font-size: var(--text-xs);">By {{ $app->admin->name ?? 'Admin' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: var(--space-6);">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
