@extends('layouts.app')
@section('title', 'Match Details')
@section('page-title', 'Match Details')
@section('breadcrumb')
<a href="{{ route('matches.index') }}">Matches</a> / Details
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Left Side: Match Details & Teams --}}
    <div>
        {{-- Match Banner --}}
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--navy-700), var(--navy-800)); border-color: rgba(16,185,129,0.2);">
            <div class="card-header" style="border-bottom-color: rgba(255,255,255,0.08);">
                <span class="card-title" style="color: white;">🏟️ Match Information</span>
                <span class="badge {{ $match->status_badge['class'] }}">{{ $match->status_badge['label'] }}</span>
            </div>
            <div class="card-body" style="color: var(--text-primary);">
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:16px; text-align:center; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.08);">
                    <div><div class="text-xs text-muted mb-1">DATE</div><div class="font-bold text-emerald">{{ $match->formatted_date }}</div></div>
                    <div><div class="text-xs text-muted mb-1">TIME</div><div class="font-bold">{{ $match->match_time }}</div></div>
                    <div><div class="text-xs text-muted mb-1">VENUE</div><div class="font-bold text-sm">{{ $match->venue }}</div></div>
                    <div><div class="text-xs text-muted mb-1">MATCH FEE</div><div class="font-bold text-gold">KSh {{ number_format($match->match_fee) }}</div></div>
                </div>

                <div class="d-flex justify-between align-center">
                    <div>
                        <div class="text-xs text-muted mb-1">LOCK DEADLINE</div>
                        <div class="text-sm {{ $match->isPastDeadline() ? 'text-red' : 'text-gold' }}">
                            {{ $match->deadline->format('d M Y, H:i') }}
                            @if($match->isPastDeadline()) <span class="badge badge-red" style="font-size:9px; margin-left:4px;">LOCKED</span> @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-muted">Created by:</span>
                        <strong class="text-sm" style="display:block;">{{ $match->creator?->name ?? 'System' }}</strong>
                    </div>
                </div>

                @if($match->notes)
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); padding:12px; margin-top:20px; font-size:13px; color: var(--text-secondary);">
                    <strong>Coach Notes:</strong> {{ $match->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Generated Teams Showcase --}}
        @if($match->matchTeams->count())
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">📋 Balanced Team Formations</span>
            </div>
            <div class="card-body">
                <div class="teams-grid">
                    @php
                        $groupedTeams = $match->matchTeams->groupBy('team_number');
                        $colors = ['team-a', 'team-b', 'team-c'];
                        $titles = ['Team A', 'Team B', 'Team C'];
                    @endphp
                    @foreach($groupedTeams as $num => $players)
                    <div class="team-col">
                        <div class="team-col-header {{ $colors[($num - 1) % 3] }}">
                            <span>{{ $titles[($num - 1) % 3] }}</span>
                            <span class="badge badge-gray" style="font-size:10px;">{{ $players->count() }} Players</span>
                        </div>
                        @foreach($players as $teamPlayer)
                        <div class="team-player">
                            <img src="{{ $teamPlayer->user?->avatar_url }}" class="mini-avatar" alt="">
                            <div style="flex:1;">
                                <div style="font-weight:600;">{{ $teamPlayer->user?->name }}</div>
                                <span class="text-xs text-muted">{{ $teamPlayer->user?->positionLabel() }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Availability Responses List --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Player Response List</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Reason / Comments</th>
                            <th style="text-align:right;">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <img src="{{ $m->avatar_url }}" style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
                                    <span style="font-weight:600;">{{ $m->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($m->position)
                                <span class="badge pos-{{ strtolower($m->position) }}">{{ $m->position }}</span>
                                @else
                                <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasRole(['admin', 'coach']))
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <form method="POST" action="{{ route('matches.availability', $match) }}" style="display:inline-flex; align-items:center; gap:6px; margin:0;">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $m->id }}">
                                        <select name="status" class="form-control" style="font-size:12px; padding:4px 8px; width:120px; min-height: 28px;" onchange="this.form.submit()">
                                            <option value="available" {{ $m->availability?->status === 'available' ? 'selected' : '' }}>🟢 Available</option>
                                            <option value="maybe" {{ $m->availability?->status === 'maybe' ? 'selected' : '' }}>🟡 Maybe</option>
                                            <option value="unavailable" {{ $m->availability?->status === 'unavailable' ? 'selected' : '' }}>🔴 Unavail</option>
                                        </select>
                                    </form>
                                    @if($m->availability?->admin_override)
                                    <span class="badge badge-orange" style="font-size:8px;" title="Overridden by Admin">Override</span>
                                    @endif
                                </div>
                                @else
                                <span class="badge {{ $m->availability->getStatusBadge()['class'] }}">
                                    {{ $m->availability->getStatusBadge()['icon'] }} {{ $m->availability->getStatusBadge()['label'] }}
                                </span>
                                @endif
                            </td>
                            <td class="text-sm text-secondary">
                                {{ $m->availability?->reason ?? '—' }}
                            </td>
                            <td class="text-xs text-muted" style="text-align:right;">
                                {{ $m->availability?->updated_at?->diffForHumans() ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Side: Quick Stats & Admin Console --}}
    <div>
        {{-- Availability Ring --}}
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">📊 Response Summary</span>
            </div>
            <div class="card-body">
                <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:20px;">
                    <div style="background: rgba(16,185,129,0.05); padding:12px; border-radius:var(--radius-sm); text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:var(--emerald-400);">{{ $availableCount }}</div>
                        <div class="text-xs text-muted">Available</div>
                    </div>
                    <div style="background: rgba(239,68,68,0.05); padding:12px; border-radius:var(--radius-sm); text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:var(--red-400);">{{ $unavailableCount }}</div>
                        <div class="text-xs text-muted">Unavailable</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); padding:12px; border-radius:var(--radius-sm); text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:var(--gold-400);">{{ $maybeCount }}</div>
                        <div class="text-xs text-muted">Maybe</div>
                    </div>
                    <div style="background: rgba(100,116,139,0.05); padding:12px; border-radius:var(--radius-sm); text-align:center;">
                        <div style="font-size:24px; font-weight:800; color:var(--text-secondary);">{{ $noResponse }}</div>
                        <div class="text-xs text-muted">No Response</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Controls Panel --}}
        @if(auth()->user()->hasRole(['admin', 'coach']))
        <div class="card mb-6" style="border-color: rgba(245,158,11,0.25);">
            <div class="card-header" style="background: rgba(245,158,11,0.03);">
                <span class="card-title" style="color: var(--gold-400);">⚙️ Admin Operations</span>
            </div>
            <div class="card-body">
                {{-- Team Generator Form --}}
                @if(!$match->matchTeams->count())
                <form method="POST" action="{{ route('matches.teams', $match) }}" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--glass-border);">
                    @csrf
                    <label class="form-label" for="num_teams">Generate Balanced Squads</label>
                    <div style="display:flex; gap:8px;">
                        <select name="num_teams" id="num_teams" class="form-control" style="flex:1;" required>
                            <option value="2">2 Teams (A vs B)</option>
                            <option value="3">3 Teams (A vs B vs C)</option>
                        </select>
                        <button type="submit" class="btn btn-gold btn-sm">
                            Generate
                        </button>
                    </div>
                    <span class="form-hint">Uses internal squad-level algorithms to partition available players evenly.</span>
                </form>
                @endif

                {{-- Score Recorder Form --}}
                @if($match->status !== 'completed')
                <form method="POST" action="{{ route('matches.result', $match) }}" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--glass-border);">
                    @csrf
                    <label class="form-label">Record Match Scoreline</label>
                    <div style="display:grid; grid-template-columns:1fr auto 1fr; gap:8px; align-items:center;">
                        <input type="number" name="home_score" min="0" placeholder="BFC" class="form-control" required>
                        <span class="text-muted font-bold">—</span>
                        <input type="number" name="away_score" min="0" placeholder="Opp" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-full" style="margin-top:10px; justify-content:center;">
                        Record Result
                    </button>
                </form>
                @else
                <div class="alert alert-success" style="padding:10px; margin-bottom:20px;">
                    🏆 Final Score: {{ $match->home_score }} - {{ $match->away_score }}
                </div>
                @endif

                {{-- Availability Locking --}}
                @if(!$match->isLocked())
                <form method="POST" action="{{ route('matches.lock', $match) }}" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--glass-border);">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm w-full" style="justify-content:center; color: var(--red-400); border-color: rgba(239,68,68,0.25);">
                        🔒 Lock Availability responses
                    </button>
                </form>
                @endif

                {{-- Delete Match --}}
                <form method="POST" action="{{ route('matches.destroy', $match) }}" onsubmit="return confirm('Are you sure you want to delete this match?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-full" style="justify-content:center;">
                        🗑️ Delete Match Event
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
