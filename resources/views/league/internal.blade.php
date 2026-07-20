@extends('layouts.app')
@section('title', 'Internal League')
@section('page-title', 'Internal League')
@section('breadcrumb')
<a href="{{ route('league.standings') }}">League</a> / Internal
@endsection

@push('styles')
<style>
/* ── Internal League Styles ───────────────────────────────────────────── */
.internal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
}

/* Team badges */
.team-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.team-badge.red   { background: rgba(239,68,68,0.15);   border: 1px solid rgba(239,68,68,0.35);   color: #f87171; }
.team-badge.blue  { background: rgba(59,130,246,0.15);  border: 1px solid rgba(59,130,246,0.35);  color: #60a5fa; }
.team-badge.white { background: rgba(148,163,184,0.12); border: 1px solid rgba(148,163,184,0.3);  color: #cbd5e1; }

/* Standings table rows */
tr.row-red   td:first-child { border-left: 3px solid #ef4444; }
tr.row-blue  td:first-child { border-left: 3px solid #3b82f6; }
tr.row-white td:first-child { border-left: 3px solid #94a3b8; }

/* Results feed */
.result-feed { display: flex; flex-direction: column; gap: 10px; }
.result-item {
    background: var(--surface-2);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: background 0.15s ease;
}
.result-item:hover { background: rgba(255,255,255,0.04); }
.result-team {
    font-weight: 700;
    font-size: 13px;
    text-align: center;
    flex: 1;
}
.result-score {
    font-size: 20px;
    font-weight: 900;
    padding: 4px 16px;
    border-radius: 8px;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    letter-spacing: 2px;
    min-width: 80px;
    text-align: center;
}
.result-meta { font-size: 11px; color: var(--text-muted); margin-top:2px; }

/* Stats cards */
.stats-panel { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media (max-width: 900px) { .stats-panel { grid-template-columns: 1fr; } }

.stat-card {
    background: var(--surface-2);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    overflow: hidden;
}
.stat-card-header {
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.stat-card-header.gold   { color: #fbbf24; background: rgba(245,158,11,0.04); }
.stat-card-header.emerald { color: #34d399; background: rgba(16,185,129,0.04); }
.stat-card-header.blue   { color: #60a5fa; background: rgba(59,130,246,0.04); }

.stat-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    transition: background 0.15s ease;
}
.stat-row:last-child { border-bottom: none; }
.stat-row:hover { background: rgba(255,255,255,0.03); }
.stat-rank {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
    color: var(--text-muted);
}
.stat-rank.first { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #000; }
.stat-row .stat-name  { flex: 1; font-size: 13px; font-weight: 500; }
.stat-row .stat-value { font-size: 18px; font-weight: 800; color: var(--text-primary); }

/* Goal event builder */
.event-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 8px;
    align-items: center;
    margin-bottom: 8px;
    padding: 8px;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--glass-border);
    border-radius: 8px;
}
.add-event-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px dashed var(--glass-border);
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
    margin-top: 8px;
}
.add-event-btn:hover { border-color: var(--emerald-400); color: var(--emerald-400); }

.remove-event-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid rgba(239,68,68,0.25);
    background: rgba(239,68,68,0.05);
    color: #f87171;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.remove-event-btn:hover { background: rgba(239,68,68,0.15); }
</style>
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────────── --}}
<div class="internal-header">
    <div>
        <h1 style="font-size:20px; font-weight:800; margin:0 0 6px;">🏆 Internal League</h1>
        <div style="font-size:13px; color:var(--text-muted);">Season 2025/2026 · Three-team round-robin competition</div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <span class="team-badge red">🔴 Team Red</span>
        <span class="team-badge blue">🔵 Team Blue</span>
        <span class="team-badge white">⚪ Team White</span>
    </div>
</div>

{{-- ── League Table ─────────────────────────────────────────────────────────── --}}
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">📊 League Table</span>
        <span class="text-xs text-muted">2025/2026 Season</span>
    </div>
    <div class="table-wrap">
        @if($standings->count())
        <table>
            <thead>
                <tr>
                    <th style="width:48px; text-align:center;">Pos</th>
                    <th>Team</th>
                    <th style="text-align:center;" title="Played">P</th>
                    <th style="text-align:center;" title="Won">W</th>
                    <th style="text-align:center;" title="Lost">L</th>
                    <th style="text-align:center;" title="Drawn">D</th>
                    <th style="text-align:center;" title="Goals For">GF</th>
                    <th style="text-align:center;" title="Goals Against">GA</th>
                    <th style="text-align:center;" title="Goal Difference">GD</th>
                    <th style="text-align:center;" title="Points">Pts</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rowColors = ['RED' => 'row-red', 'BLUE' => 'row-blue', 'WHT' => 'row-white'];
                    $teamEmoji = ['Team Red' => '🔴', 'Team Blue' => '🔵', 'Team White' => '⚪'];
                @endphp
                @foreach($standings as $i => $s)
                @php
                    $rowClass = $rowColors[$s->team?->short_name] ?? '';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td style="text-align:center;" class="rank {{ $i === 0 ? 'top' : '' }}">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:16px;">{{ $teamEmoji[$s->team?->name] ?? '🏅' }}</span>
                            <strong>{{ $s->team?->name ?? 'Unknown' }}</strong>
                            @if($i === 0)
                            <span class="badge badge-gold" style="font-size:9px;">Leader</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center;">{{ $s->played }}</td>
                    <td style="text-align:center; color:var(--emerald-400); font-weight:600;">{{ $s->wins }}</td>
                    <td style="text-align:center; color:var(--red-400);">{{ $s->losses }}</td>
                    <td style="text-align:center; color:var(--gold-400);">{{ $s->draws }}</td>
                    <td style="text-align:center; color:var(--text-secondary);">{{ $s->goals_for }}</td>
                    <td style="text-align:center; color:var(--text-secondary);">{{ $s->goals_against }}</td>
                    <td style="text-align:center; color:{{ $s->goal_difference >= 0 ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                        {{ $s->goal_difference > 0 ? '+' : '' }}{{ $s->goal_difference }}
                    </td>
                    <td style="text-align:center;" class="pts">{{ $s->points }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🏆</div>
            <div class="empty-state-title">Season hasn't started yet</div>
            <p class="text-xs text-muted">Record the first result below to kick off the internal league!</p>
        </div>
        @endif
    </div>
</div>

{{-- ── Stats Panel ──────────────────────────────────────────────────────────── --}}
<div class="stats-panel mb-6">
    {{-- Top Scorers --}}
    <div class="stat-card">
        <div class="stat-card-header gold">⚽ Top Scorers</div>
        @if($topScorers->count())
        @foreach($topScorers as $idx => $ts)
        <div class="stat-row">
            <div class="stat-rank {{ $idx === 0 ? 'first' : '' }}">{{ $idx + 1 }}</div>
            <img src="{{ $ts->player?->avatar_url }}" style="width:28px; height:28px; border-radius:50%; object-fit:cover;" alt="">
            <div class="stat-name">
                {{ $ts->player?->name ?? '—' }}
                <div class="result-meta">{{ $ts->player?->positionLabel() }}</div>
            </div>
            <div class="stat-value" style="color:#fbbf24;">{{ $ts->goals }}</div>
        </div>
        @endforeach
        @else
        <div style="padding:24px; text-align:center; color:var(--text-muted); font-size:13px;">
            No goals recorded yet
        </div>
        @endif
    </div>

    {{-- Most Assists --}}
    <div class="stat-card">
        <div class="stat-card-header emerald">🎯 Most Assists</div>
        @if($topAssists->count())
        @foreach($topAssists as $idx => $ta)
        <div class="stat-row">
            <div class="stat-rank {{ $idx === 0 ? 'first' : '' }}">{{ $idx + 1 }}</div>
            <img src="{{ $ta->player?->avatar_url }}" style="width:28px; height:28px; border-radius:50%; object-fit:cover;" alt="">
            <div class="stat-name">
                {{ $ta->player?->name ?? '—' }}
                <div class="result-meta">{{ $ta->player?->positionLabel() }}</div>
            </div>
            <div class="stat-value" style="color:#34d399;">{{ $ta->assists }}</div>
        </div>
        @endforeach
        @else
        <div style="padding:24px; text-align:center; color:var(--text-muted); font-size:13px;">
            No assists recorded yet
        </div>
        @endif
    </div>

    {{-- Best Attendance --}}
    <div class="stat-card">
        <div class="stat-card-header blue">📅 Best Attendance</div>
        @if($attendance->count())
        @foreach($attendance as $idx => $att)
        <div class="stat-row">
            <div class="stat-rank {{ $idx === 0 ? 'first' : '' }}">{{ $idx + 1 }}</div>
            <img src="{{ $att->player?->avatar_url }}" style="width:28px; height:28px; border-radius:50%; object-fit:cover;" alt="">
            <div class="stat-name">
                {{ $att->player?->name ?? '—' }}
                <div class="result-meta">{{ $att->matches }} {{ Str::plural('match', $att->matches) }}</div>
            </div>
            <div class="stat-value" style="color:#60a5fa;">{{ $att->matches }}</div>
        </div>
        @endforeach
        @else
        <div style="padding:24px; text-align:center; color:var(--text-muted); font-size:13px;">
            No data yet
        </div>
        @endif
    </div>
</div>

{{-- ── Results & Record Form ─────────────────────────────────────────────────── --}}
<div class="dashboard-grid">
    {{-- Recent Results --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Recent Results</span>
                <span class="badge badge-gray" style="font-size:10px;">{{ $results->count() }} results</span>
            </div>
            <div class="card-body">
                @if($results->count())
                <div class="result-feed">
                    @foreach($results as $r)
                    @php
                        $homeColor = $rowColors[$r->homeTeam?->short_name] ?? '';
                        $awayColor = $rowColors[$r->awayTeam?->short_name] ?? '';
                        $homeEmoji = $teamEmoji[$r->homeTeam?->name] ?? '🏅';
                        $awayEmoji = $teamEmoji[$r->awayTeam?->name] ?? '🏅';
                        $goals = $r->goalEvents;
                    @endphp
                    <div class="result-item">
                        <div class="result-team">
                            <div>{{ $homeEmoji }} {{ $r->homeTeam?->name }}</div>
                            <div class="result-meta">Home</div>
                        </div>
                        <div>
                            <div class="result-score">{{ $r->home_score }} – {{ $r->away_score }}</div>
                            <div class="result-meta" style="text-align:center;">
                                {{ $r->created_at->format('d M Y') }}
                                @if($r->recorder) · {{ $r->recorder->name }} @endif
                            </div>
                            @if($goals->where('type','goal')->count())
                            <div style="text-align:center; margin-top:4px; font-size:10px; color:var(--text-muted);">
                                ⚽ {{ $goals->where('type','goal')->map(fn($g) => $g->player?->name)->filter()->implode(', ') }}
                            </div>
                            @endif
                        </div>
                        <div class="result-team">
                            <div>{{ $awayEmoji }} {{ $r->awayTeam?->name }}</div>
                            <div class="result-meta">Away</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state" style="padding:32px 20px;">
                    <div class="empty-state-icon">📅</div>
                    <p class="text-xs text-muted">No results recorded yet. Record the first match!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Record Result Form --}}
    <div>
        @if(auth()->user()->hasRole(['admin', 'coach']))
        <div class="card" style="border-color: rgba(16,185,129,0.2);">
            <div class="card-header" style="background: rgba(16,185,129,0.03);">
                <span class="card-title" style="color: var(--emerald-400);">📝 Record Internal Result</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('league.internal.result') }}" id="internalResultForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="home_team_id">Home Team <span class="required">*</span></label>
                        <select name="home_team_id" id="home_team_id" class="form-control" required>
                            <option value="">-- Choose Home Team --</option>
                            @foreach($internalTeams as $t)
                            <option value="{{ $t->id }}">
                                {{ ['RED'=>'🔴','BLUE'=>'🔵','WHT'=>'⚪'][$t->short_name] ?? '🏅' }} {{ $t->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="away_team_id">Away Team <span class="required">*</span></label>
                        <select name="away_team_id" id="away_team_id" class="form-control" required>
                            <option value="">-- Choose Away Team --</option>
                            @foreach($internalTeams as $t)
                            <option value="{{ $t->id }}">
                                {{ ['RED'=>'🔴','BLUE'=>'🔵','WHT'=>'⚪'][$t->short_name] ?? '🏅' }} {{ $t->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="int_home_score">Home Score <span class="required">*</span></label>
                            <input type="number" name="home_score" id="int_home_score" min="0" class="form-control" required placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="int_away_score">Away Score <span class="required">*</span></label>
                            <input type="number" name="away_score" id="int_away_score" min="0" class="form-control" required placeholder="0">
                        </div>
                    </div>

                    {{-- Goal / Assist Events --}}
                    <div style="margin-bottom: 16px;">
                        <label class="form-label">⚽ Goal & Assist Events <span class="text-xs text-muted">(optional)</span></label>
                        <div id="eventsContainer"></div>
                        <button type="button" class="add-event-btn" onclick="addEventRow()">
                            + Add Goal / Assist
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="int_notes">Notes</label>
                        <textarea name="notes" id="int_notes" class="form-control" rows="2" placeholder="Any match highlights..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" style="justify-content:center; margin-top:12px;">
                        Record Result & Update Standings
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const members = @json($members->map(fn($m) => ['id' => $m->id, 'name' => $m->name]));
let eventCount = 0;

function addEventRow() {
    const container = document.getElementById('eventsContainer');
    const idx = eventCount++;

    const memberOptions = members.map(m =>
        `<option value="${m.id}">${m.name}</option>`
    ).join('');

    const row = document.createElement('div');
    row.className = 'event-row';
    row.id = `event-row-${idx}`;
    row.innerHTML = `
        <select name="events[${idx}][user_id]" class="form-control" required>
            <option value="">-- Player --</option>
            ${memberOptions}
        </select>
        <select name="events[${idx}][type]" class="form-control" required>
            <option value="goal">⚽ Goal</option>
            <option value="assist">🎯 Assist</option>
        </select>
        <button type="button" class="remove-event-btn" onclick="document.getElementById('event-row-${idx}').remove()">×</button>
    `;
    container.appendChild(row);
}
</script>
@endpush
@endsection
