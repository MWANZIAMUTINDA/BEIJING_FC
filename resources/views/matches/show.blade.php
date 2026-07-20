@extends('layouts.app')
@section('title', 'Match Details')
@section('page-title', 'Match Details')
@section('breadcrumb')
<a href="{{ route('matches.index') }}">Matches</a> / Details
@endsection

@push('styles')
<style>
/* ── Team Cards ─────────────────────────────────────────────────────────── */
.teams-showcase { display: grid; gap: 20px; }
.teams-showcase.two-teams  { grid-template-columns: 1fr 1fr; }
.teams-showcase.three-teams { grid-template-columns: 1fr 1fr 1fr; }
@media (max-width: 900px) {
    .teams-showcase.two-teams,
    .teams-showcase.three-teams { grid-template-columns: 1fr; }
}

.team-card {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--glass-border);
    background: var(--surface-1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.team-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }

.team-card-header {
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.5px;
}
.team-card-header.red   { background: linear-gradient(135deg, #7f1d1d, #ef4444); color: #fff; }
.team-card-header.blue  { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; }
.team-card-header.white { background: linear-gradient(135deg, #334155, #94a3b8); color: #fff; }

.team-balance-bar {
    padding: 10px 18px;
    border-bottom: 1px solid var(--glass-border);
    background: rgba(255,255,255,0.02);
}
.team-balance-label {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: var(--text-muted);
    margin-bottom: 5px;
}
.balance-track {
    height: 6px;
    border-radius: 99px;
    background: rgba(255,255,255,0.06);
    overflow: hidden;
}
.balance-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1s ease;
}
.balance-fill.high   { background: linear-gradient(90deg, #10b981, #34d399); }
.balance-fill.medium { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.balance-fill.low    { background: linear-gradient(90deg, #ef4444, #f87171); }

.pos-group { padding: 10px 0 4px; }
.pos-group-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    color: var(--text-muted);
    padding: 0 16px 6px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 6px;
}
.pos-group-label::after { content:''; flex:1; height:1px; background: var(--glass-border); }

.team-player-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 16px;
    border-radius: 6px;
    margin: 2px 8px;
    cursor: pointer;
    transition: background 0.15s ease;
}
.team-player-row:hover { background: rgba(255,255,255,0.05); }
.team-player-row .player-name { font-size: 13px; font-weight: 500; flex: 1; }
.team-player-row .mini-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }

/* Position dot colors */
.pos-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.pos-dot.GK { background: #f59e0b; }
.pos-dot.DF { background: #3b82f6; }
.pos-dot.MF { background: #10b981; }
.pos-dot.FW { background: #ef4444; }

/* Swap button */
.swap-btn {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 99px;
    border: 1px solid rgba(255,255,255,0.12);
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.swap-btn:hover { border-color: var(--emerald-400); color: var(--emerald-400); background: rgba(16,185,129,0.08); }

/* Balance Overview Banner */
.balance-banner {
    background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(59,130,246,0.08));
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: 12px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 16px;
    flex-wrap: wrap;
}
.balance-banner-score {
    font-size: 42px;
    font-weight: 900;
    background: linear-gradient(135deg, #10b981, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}
.balance-banner-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-top: 4px; }
.pos-breakdown {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.pos-chip {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
}

/* Swap Modal */
.swap-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.swap-modal-backdrop.open { opacity: 1; pointer-events: all; }
.swap-modal {
    background: var(--surface-2);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 28px;
    width: 400px;
    max-width: 90vw;
    transform: scale(0.95);
    transition: transform 0.2s ease;
}
.swap-modal-backdrop.open .swap-modal { transform: scale(1); }
.swap-modal-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; }
</style>
@endpush

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

        {{-- ══ Module 8: Generated Teams Showcase ══ --}}
        @if($match->matchTeams->count())
        @php
            $teamData    = [];
            $teamColors  = ['Team Red' => 'red', 'Team Blue' => 'blue', 'Team White' => 'white'];
            $teamEmojis  = ['Team Red' => '🔴', 'Team Blue' => '🔵', 'Team White' => '⚪'];
            $colorFallback = ['red','blue','white'];
            $emojiFallback = ['🔴','🔵','⚪'];

            foreach ($match->matchTeams as $idx => $mt) {
                $players = \App\Models\User::whereIn('id', $mt->players_list ?? [])->get()->keyBy('id');
                $ordered = collect($mt->players_list ?? [])->map(fn($id) => $players->get($id))->filter();
                $byPos   = $ordered->groupBy('position');
                $color   = $teamColors[$mt->team_label] ?? ($colorFallback[$idx % 3]);
                $emoji   = $teamEmojis[$mt->team_label] ?? ($emojiFallback[$idx % 3]);
                $score   = $mt->position_balance_score ?? 0;
                $teamData[] = compact('mt','ordered','byPos','color','emoji','score');
            }
            $teamCount     = count($teamData);
            $overallScore  = $teamCount > 0 ? (int)round(array_sum(array_column($teamData,'score')) / $teamCount) : 0;
            $posOrder      = ['GK','DF','MF','FW'];
            $posLabels     = ['GK' => '🧤 Goalkeeper', 'DF' => '🛡️ Defenders', 'MF' => '⚙️ Midfielders', 'FW' => '⚡ Forwards'];
        @endphp

        {{-- Balance Overview Banner --}}
        <div class="balance-banner mb-2">
            <div>
                <div class="balance-banner-score">{{ $overallScore }}%</div>
                <div class="balance-banner-label">Score Balance</div>
            </div>
            <div style="flex:1; padding:0 16px;">
                <div style="font-weight:700; font-size:14px; margin-bottom:8px;">⚖️ Squad Balance Analysis</div>
                <div class="pos-breakdown">
                    @php
                        $allPlayers = $match->matchTeams->flatMap(fn($mt) => $mt->players_list ?? []);
                        $allUsers   = \App\Models\User::whereIn('id', $allPlayers)->get();
                        $posCounts  = $allUsers->countBy('position');
                    @endphp
                    @foreach(['GK','DF','MF','FW'] as $pos)
                    <div class="pos-chip">
                        <span class="pos-dot {{ $pos }}"></span>
                        {{ $pos }} <strong>{{ $posCounts->get($pos, 0) }}</strong>
                    </div>
                    @endforeach
                    <div class="pos-chip">
                        👥 <strong>{{ $allPlayers->count() }}</strong> total
                    </div>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin','coach']))
            <div>
                <form method="POST" action="{{ route('matches.teams', $match) }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="num_teams" value="{{ $teamCount }}">
                    <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Regenerate teams? This will reset any manual swaps.')">
                        🔄 Regenerate
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Team Cards --}}
        <div class="card mb-6">
            <div class="card-header">
                <span class="card-title">📋 Balanced Team Formations</span>
                @if(auth()->user()->hasRole(['admin','coach']))
                <span class="text-xs text-muted">Click <strong>Swap →</strong> on any player to move them</span>
                @endif
            </div>
            <div class="card-body" style="padding-top:12px;">
                <div class="teams-showcase {{ $teamCount === 2 ? 'two-teams' : 'three-teams' }}">
                    @foreach($teamData as $td)
                    @php $mt = $td['mt']; $color = $td['color']; $emoji = $td['emoji']; $score = $td['score']; @endphp
                    <div class="team-card">
                        {{-- Header --}}
                        <div class="team-card-header {{ $color }}">
                            <span>{{ $emoji }} {{ $mt->team_label }}</span>
                            <span style="font-size:12px; opacity:0.85;">{{ $td['ordered']->count() }} players</span>
                        </div>

                        {{-- Balance bar --}}
                        <div class="team-balance-bar">
                            <div class="team-balance-label">
                                <span>Team Balance</span>
                                <span style="font-weight:700; color: {{ $score >= 75 ? 'var(--emerald-400)' : ($score >= 50 ? 'var(--gold-400)' : 'var(--red-400)') }};">{{ $score }}%</span>
                            </div>
                            <div class="balance-track">
                                <div class="balance-fill {{ $score >= 75 ? 'high' : ($score >= 50 ? 'medium' : 'low') }}" style="width: {{ $score }}%;"></div>
                            </div>
                        </div>

                        {{-- Players grouped by position --}}
                        @foreach($posOrder as $pos)
                        @php $group = $td['byPos']->get($pos, collect()); @endphp
                        @if($group->count())
                        <div class="pos-group">
                            <div class="pos-group-label">
                                <span class="pos-dot {{ $pos }}"></span>
                                {{ $posLabels[$pos] }}
                                <span style="color: var(--text-primary); opacity:0.5; margin-left:auto; margin-right:4px; font-size:10px;">{{ $group->count() }}</span>
                            </div>
                            @foreach($group as $player)
                            <div class="team-player-row" id="player-row-{{ $player->id }}-{{ $color }}">
                                <img src="{{ $player->avatar_url }}" class="mini-avatar" alt="{{ $player->name }}">
                                <span class="player-name">{{ $player->name }}</span>
                                @if(auth()->user()->hasRole(['admin','coach']) && $teamCount > 1)
                                <button type="button" class="swap-btn"
                                    onclick="openSwapModal({{ $player->id }}, '{{ e($player->name) }}', '{{ $mt->team_label }}', {{ json_encode($match->matchTeams->pluck('team_label')->filter(fn($l) => $l !== $mt->team_label)->values()) }})">
                                    Swap →
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach

                        {{-- Unpositioned players (no position set) --}}
                        @php $noPos = $td['ordered']->whereNull('position'); @endphp
                        @if($noPos->count())
                        <div class="pos-group">
                            <div class="pos-group-label">❓ Unassigned Position</div>
                            @foreach($noPos as $player)
                            <div class="team-player-row">
                                <img src="{{ $player->avatar_url }}" class="mini-avatar" alt="{{ $player->name }}">
                                <span class="player-name">{{ $player->name }}</span>
                                @if(auth()->user()->hasRole(['admin','coach']) && $teamCount > 1)
                                <button type="button" class="swap-btn"
                                    onclick="openSwapModal({{ $player->id }}, '{{ e($player->name) }}', '{{ $mt->team_label }}', {{ json_encode($match->matchTeams->pluck('team_label')->filter(fn($l) => $l !== $mt->team_label)->values()) }})">
                                    Swap →
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
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

    {{-- Right Side: Stats & Admin Console --}}
    <div>
        {{-- Response Summary --}}
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
                    <label class="form-label" for="num_teams">⚡ Generate Balanced Squads</label>
                    <div style="display:flex; gap:8px;">
                        <select name="num_teams" id="num_teams" class="form-control" style="flex:1;" required>
                            <option value="2">2 Teams (Red vs Blue)</option>
                            <option value="3">3 Teams (Red / Blue / White)</option>
                        </select>
                        <button type="submit" class="btn btn-gold btn-sm">Generate</button>
                    </div>
                    <span class="form-hint">Separates GK→DF→MF→FW, then balances team sizes automatically.</span>
                </form>
                @else
                <div style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--glass-border);">
                    <div class="text-xs text-muted mb-2">TEAM GENERATION</div>
                    <div style="font-size:13px; color:var(--emerald-400); font-weight:600; margin-bottom:8px;">✅ Teams already generated</div>
                    <form method="POST" action="{{ route('matches.teams', $match) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="num_teams" value="{{ $match->matchTeams->count() }}">
                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Regenerate teams? This will reset any manual swaps.')">
                            🔄 Regenerate Teams
                        </button>
                    </form>
                </div>
                @endif

                {{-- Score Recorder Form --}}
                @if($match->status !== 'completed')
                <form method="POST" action="{{ route('matches.result', $match) }}" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid var(--glass-border);">
                    @csrf
                    <label class="form-label">📝 Record Match Scoreline</label>
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

{{-- ══ Manual Swap Modal ══ --}}
@if(auth()->user()->hasRole(['admin','coach']))
<div class="swap-modal-backdrop" id="swapModalBackdrop" onclick="closeSwapModal(event)">
    <div class="swap-modal" onclick="event.stopPropagation()">
        <div class="swap-modal-title">↔️ Move Player to Another Team</div>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">
            Moving: <strong id="swapPlayerName" style="color:var(--text-primary);"></strong>
            from <strong id="swapFromTeam" style="color:var(--text-primary);"></strong>
        </p>
        <form method="POST" action="{{ route('matches.teams.swap', $match) }}" id="swapForm">
            @csrf
            <input type="hidden" name="player_id" id="swapPlayerId">
            <input type="hidden" name="from_team" id="swapFromTeamInput">
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Move to Team</label>
                <select name="to_team" id="swapToTeam" class="form-control" required>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                    ✅ Confirm Swap
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeSwapModal()" style="flex:1; justify-content:center;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
function openSwapModal(playerId, playerName, fromTeam, otherTeams) {
    document.getElementById('swapPlayerId').value     = playerId;
    document.getElementById('swapPlayerName').textContent = playerName;
    document.getElementById('swapFromTeam').textContent   = fromTeam;
    document.getElementById('swapFromTeamInput').value    = fromTeam;

    const sel = document.getElementById('swapToTeam');
    sel.innerHTML = '';
    otherTeams.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t; opt.textContent = t;
        sel.appendChild(opt);
    });

    document.getElementById('swapModalBackdrop').classList.add('open');
}
function closeSwapModal(e) {
    if (!e || e.target === document.getElementById('swapModalBackdrop')) {
        document.getElementById('swapModalBackdrop').classList.remove('open');
    }
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSwapModal();
});
</script>
@endpush
@endsection
