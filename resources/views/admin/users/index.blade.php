@extends('layouts.app')

@section('title', 'Manage Users — Artevo')

@section('content')
    <section class="av-section av-section--white" style="padding-top: var(--space-10);">
        <div class="container">
            <x-tag>Admin</x-tag>
            <h1 style="margin-top: var(--space-4);">Users &amp; roles</h1>
            <p style="max-width: 560px;">{{ $users->total() }} registered accounts. Administrator and Curator
                roles must be assigned here — visitors can only self-upgrade to Collector.</p>

            <div style="overflow-x: auto; margin-top: var(--space-6); border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <table style="width: 100%; border-collapse: collapse; font-size: var(--text-sm);">
                    <thead>
                        <tr style="background: var(--porcelain-100); text-align: left;">
                            <th style="padding: var(--space-3) var(--space-4);">User</th>
                            <th style="padding: var(--space-3) var(--space-4);">Email</th>
                            <th style="padding: var(--space-3) var(--space-4);">Verified</th>
                            <th style="padding: var(--space-3) var(--space-4);">Role</th>
                            <th style="padding: var(--space-3) var(--space-4);"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $listedUser)
                            <tr style="border-top: 1px solid var(--color-border);">
                                <td style="padding: var(--space-3) var(--space-4);">
                                    <div class="flex gap-3" style="align-items: center;">
                                        <x-avatar :user="$listedUser" />
                                        {{ $listedUser->name }}
                                    </div>
                                </td>
                                <td style="padding: var(--space-3) var(--space-4); color: var(--ink-700);">{{ $listedUser->email }}</td>
                                <td style="padding: var(--space-3) var(--space-4);">
                                    @if ($listedUser->hasVerifiedEmail())
                                        <x-tag variant="success" class="av-tag--pill">Verified</x-tag>
                                    @else
                                        <x-tag variant="muted" class="av-tag--pill">Pending</x-tag>
                                    @endif
                                </td>
                                <td style="padding: var(--space-3) var(--space-4);">
                                    <x-tag>{{ $listedUser->roleLabel() }}</x-tag>
                                </td>
                                <td style="padding: var(--space-3) var(--space-4);">
                                    @if ($listedUser->isNot(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.update-role', $listedUser) }}" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" style="font-size: var(--text-sm); padding: 0.4rem 0.6rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                                                @foreach (\App\Models\User::ROLES as $roleOption)
                                                    <option value="{{ $roleOption }}" @selected($listedUser->role === $roleOption)>{{ ucfirst($roleOption) }}</option>
                                                @endforeach
                                            </select>
                                            <x-button type="submit" variant="outline-dark" style="padding: 0.4rem 0.8rem; font-size: var(--text-xs);">Save</x-button>
                                        </form>
                                    @else
                                        <span style="color: var(--stone-600); font-size: var(--text-xs);">This is you</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: var(--space-6);">
                {{ $users->links() }}
            </div>
        </div>
    </section>
@endsection
