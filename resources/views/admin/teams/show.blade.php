@extends('layouts.app')
@section('title', 'Team Roster')
@section('page-title', 'Team Roster')
@section('breadcrumb')
<a href="{{ route('admin.teams.index') }}">Teams</a> / Details
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Left: Player Roster --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Roster: {{ $team->name }} ({{ $team->players->count() }} Players)</span>
            </div>
            <div class="table-wrap">
                @if($team->players->count())
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Jersey</th>
                            <th>Player</th>
                            <th>Position</th>
                            <th>Phone</th>
                            <th>Nationality</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($team->players as $p)
                        <tr>
                            <td style="text-align: center;">
                                @if($p->jersey_number)
                                <span style="font-family:'Outfit',sans-serif; font-weight:800; font-size:15px; color:var(--gold-400);">{{ $p->jersey_number }}</span>
                                @else
                                <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <img src="{{ $p->avatar_url }}" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid var(--glass-border);">
                                    <span style="font-weight:600;">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($p->position)
                                <span class="badge pos-{{ strtolower($p->position) }}">{{ $p->position }}</span>
                                @else
                                <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ $p->phone }}</td>
                            <td class="text-sm">{{ $p->nationality ?? 'Kenyan' }}</td>
                            <td>
                                @if($p->is_active)
                                <span class="badge badge-green">Active</span>
                                @else
                                <span class="badge badge-red">Suspended</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state" style="padding: 40px 20px;">
                    <div class="empty-state-icon">👥</div>
                    <div class="empty-state-title">No players assigned to this team</div>
                    <p class="text-xs text-muted">Edit a player profile to link them to this team.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Team Overview --}}
    <div>
        <div class="card mb-6" style="border-color: rgba(59,130,246,0.2);">
            <div class="card-header">
                <span class="card-title">🛡️ Team Configuration</span>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-sm text-secondary">Team Name:</span>
                        <strong class="text-sm">{{ $team->name }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-sm text-secondary">Short Code:</span>
                        <span class="badge badge-blue" style="font-size:11px;">{{ $team->short_name }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-sm text-secondary">Badge Color:</span>
                        <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: {{ $team->color }}; border: 1.5px solid var(--glass-border);"></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-sm text-secondary">Kit Color:</span>
                        @if($team->kit_color)
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="display: inline-block; width: 14px; height: 14px; border-radius: 2px; background-color: {{ $team->kit_color }}; border: 1px solid var(--glass-border);"></span>
                            <span class="text-xs text-muted">{{ strtoupper($team->kit_color) }}</span>
                        </div>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-sm text-secondary">Status:</span>
                        @if($team->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-red">Inactive</span>
                        @endif
                    </div>
                </div>

                <div style="display:flex; gap:12px; margin-top:24px;">
                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-primary btn-sm w-full" style="justify-content:center;">Edit Team</a>
                    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary btn-sm w-full" style="justify-content:center;">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
