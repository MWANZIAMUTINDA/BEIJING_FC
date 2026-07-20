@extends('layouts.app')
@section('title', 'Member Details - ' . $user->name)
@section('page-title', 'Member Profile')
@section('breadcrumb')
<a href="{{ route('admin.users.index') }}">Members</a> / {{ $user->name }}
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Left side: Player Details & Cards --}}
    <div>
        {{-- Profile Hero Card --}}
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-750) 100%);">
            <div class="card-body" style="padding:28px 24px; display:flex; align-items:center; gap:24px;">
                <img src="{{ $user->avatar_url }}" style="width:96px; height:96px; border-radius:50%; object-fit:cover; border:3px solid var(--emerald-400); flex-shrink:0;">
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <h2 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:900; color:var(--text-primary); margin:0;">{{ $user->name }}</h2>
                        @if($user->jersey_number)
                        <span style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--gold-400); background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); padding:2px 8px; border-radius:var(--radius-sm);">#{{ $user->jersey_number }}</span>
                        @endif
                    </div>
                    <div style="margin-top:6px;">
                        <span class="badge badge-{{ $user->role_color }}" style="font-size:10px; margin-right:6px;">{{ $user->role_label }}</span>
                        @if($user->position)
                        <span class="badge pos-{{ strtolower($user->position) }}">{{ $user->positionLabel() }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-muted" style="margin-top:12px;">
                        Member since: <strong>{{ $user->date_joined ? $user->date_joined->format('d M Y') : 'N/A' }}</strong>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    @if($user->is_active)
                    <span class="badge badge-green" style="font-size:11px; padding:6px 12px;">Active Member</span>
                    @else
                    <span class="badge badge-red" style="font-size:11px; padding:6px 12px;">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contact & Playing Info --}}
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">📋 Member Info</span>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Username</div>
                        <div class="font-bold text-sm">{{ $user->username }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Email Address</div>
                        <div class="font-bold text-sm">{{ $user->email ?? 'Not set' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Phone Number</div>
                        <div class="font-bold text-sm">{{ $user->phone }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Position</div>
                        <div class="font-bold text-sm">{{ $user->positionLabel() }} ({{ $user->position ?? '—' }})</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Nationality</div>
                        <div class="font-bold text-sm">{{ $user->nationality ?? 'Kenyan' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Assigned Team</div>
                        <div class="font-bold text-sm">
                            @if($user->team)
                            <a href="{{ route('admin.teams.show', $user->team) }}" class="text-emerald" style="text-decoration:none; font-weight:700;">
                                {{ $user->team->name }} ({{ $user->team->short_name }})
                            </a>
                            @else
                            <span class="text-muted">Independent</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Emergency Contact Card --}}
        <div class="card mb-6" style="border-left: 4px solid var(--gold-400);">
            <div class="card-header">
                <span class="card-title" style="color:var(--gold-400);">🚨 Emergency Contact</span>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Contact Name</div>
                        <div class="font-bold text-sm">{{ $user->emergency_contact ?? 'Not set' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Contact Phone</div>
                        <div class="font-bold text-sm">{{ $user->emergency_phone ?? 'Not set' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Match Appearances / Availability Log --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🏟️ Match Attendance Log</span>
            </div>
            <div class="table-wrap">
                @if($user->availabilities->count())
                <table>
                    <thead>
                        <tr>
                            <th>Match / Date</th>
                            <th>Venue</th>
                            <th>Status Response</th>
                            <th>Kick-off</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->availabilities as $avail)
                        @if($avail->match)
                        <tr>
                            <td>
                                <strong>vs {{ $avail->match->opponent }}</strong><br>
                                <span class="text-xs text-muted">{{ $avail->match->formatted_date }}</span>
                            </td>
                            <td class="text-sm">{{ $avail->match->venue }}</td>
                            <td>
                                <span class="badge {{ $avail->getStatusBadge()['class'] }}">
                                    {{ $avail->getStatusBadge()['icon'] }} {{ $avail->getStatusBadge()['label'] }}
                                </span>
                            </td>
                            <td class="text-xs">{{ $avail->match->match_time }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state" style="padding:32px 20px;">
                    <p class="text-xs text-muted">No match availability registered yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right side: Account Balance Summary & Recent Payments --}}
    <div>
        {{-- Account Balance Summary --}}
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">💰 Financial Standing</span>
            </div>
            <div class="card-body">
                @if($user->balance)
                <div style="text-align:center; padding:12px 0;">
                    <div style="font-size:32px; font-weight:800; font-family:'Outfit',sans-serif; color:{{ $user->balance->isInCredit() ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                        KSh {{ number_format(abs($user->balance->balance)) }}
                    </div>
                    <div class="text-xs text-muted" style="margin-top:4px;">
                        {{ $user->balance->isInCredit() ? 'Advance Credit balance' : 'Outstanding debt balance' }}
                    </div>
                    <div class="badge {{ $user->balance->getStatusClass() }}" style="margin-top:10px;">
                        {{ $user->balance->getStatusLabel() }}
                    </div>
                </div>
                <hr class="divider">
                <div class="d-flex justify-between" style="font-size:13px; margin-bottom:8px;">
                    <span class="text-secondary">Total Contributed</span>
                    <strong class="text-emerald">KSh {{ number_format($user->balance->total_paid) }}</strong>
                </div>
                <div class="d-flex justify-between" style="font-size:13px; margin-bottom:8px;">
                    <span class="text-secondary">Total Owed</span>
                    <strong class="text-red">KSh {{ number_format($user->balance->total_owed) }}</strong>
                </div>
                @if($user->balance->last_payment_at)
                <div class="d-flex justify-between mt-2" style="font-size:13px;">
                    <span class="text-secondary">Last Active Payment</span>
                    <span>{{ $user->balance->last_payment_at->format('d M Y') }}</span>
                </div>
                @endif
                @else
                <p class="text-xs text-muted text-center">No balance details calculated yet.</p>
                @endif
            </div>
        </div>

        {{-- Member's Payments Log --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">💳 Payments Log</span>
            </div>
            <div style="max-height: 480px; overflow-y: auto;">
                @forelse($user->payments as $p)
                <div style="padding:12px 18px; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div class="text-sm font-bold" style="color:var(--text-primary);">{{ $p->getTypeLabel() }}</div>
                        <div class="text-xs text-muted">{{ $p->created_at->format('d M Y') }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="text-emerald font-bold" style="font-size:13px;">KSh {{ number_format($p->amount) }}</div>
                        <span class="badge {{ $p->getStatusBadge()['class'] }}" style="font-size:9px;">{{ $p->getStatusBadge()['label'] }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:30px;">
                    <p class="text-xs text-muted">No payments recorded for this member.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Actions --}}
        <div style="margin-top: 24px;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary w-full" style="justify-content:center;">Edit Member Profile</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-full mt-2" style="justify-content:center;">Back to Members List</a>
        </div>
    </div>
</div>
@endsection
