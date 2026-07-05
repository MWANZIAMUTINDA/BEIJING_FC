@extends('layouts.app')
@section('title', 'League Standings')
@section('page-title', 'League Standings')

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🏆 Season Standings</span>
        <form method="GET" action="{{ route('league.standings') }}" style="display:flex; gap:8px; align-items:center;">
            <select name="season" class="form-control form-control-sm" style="width:140px;" onchange="this.form.submit()">
                @foreach($seasons as $s)
                <option value="{{ $s }}" {{ $season === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
                <option value="2025/2026" {{ $season === '2025/2026' ? 'selected' : '' }}>2025/2026</option>
            </select>
        </form>
    </div>
    <div class="table-wrap">
        @if($standings->count())
        <table>
            <thead>
                <tr>
                    <th style="width:50px; text-align:center;">Pos</th>
                    <th>Team</th>
                    <th style="text-align:center;">P</th>
                    <th style="text-align:center;">W</th>
                    <th style="text-align:center;">D</th>
                    <th style="text-align:center;">L</th>
                    <th style="text-align:center;">GF</th>
                    <th style="text-align:center;">GA</th>
                    <th style="text-align:center;">GD</th>
                    <th style="text-align:center;">Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach($standings as $i => $s)
                <tr class="{{ $s->team?->name === 'Beijing FC' ? 'highlight-row' : '' }}">
                    <td style="text-align:center;" class="rank {{ $i===0?'top':'' }}">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $s->team?->name ?? 'Unknown Team' }}</strong>
                        @if($s->team?->name === 'Beijing FC')
                        <span class="badge badge-emerald" style="font-size:9px; margin-left:6px;">Our Club</span>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $s->played }}</td>
                    <td style="text-align:center;">{{ $s->wins }}</td>
                    <td style="text-align:center;">{{ $s->draws }}</td>
                    <td style="text-align:center;">{{ $s->losses }}</td>
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
            <div class="empty-state-title">No standings records found</div>
            <p class="text-xs text-muted">Create league teams and record match results to calculate points.</p>
        </div>
        @endif
    </div>
</div>

<div class="dashboard-grid">
    {{-- Left: Recent Results --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Recent Results</span>
                <a href="{{ route('league.history') }}" class="btn btn-secondary btn-sm">Full History</a>
            </div>
            <div class="table-wrap">
                @if($results->count())
                <table>
                    <thead>
                        <tr>
                            <th>Match</th>
                            <th>Score</th>
                            <th>Recorded By</th>
                            <th style="text-align:right;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $r)
                        <tr>
                            <td>
                                <strong>{{ $r->homeTeam?->short_name }}</strong>
                                <span class="text-muted">vs</span>
                                <strong>{{ $r->awayTeam?->short_name }}</strong>
                            </td>
                            <td>
                                <strong class="text-emerald">{{ $r->home_score }} - {{ $r->away_score }}</strong>
                            </td>
                            <td class="text-xs text-muted">{{ $r->recorder?->name }}</td>
                            <td class="text-xs text-muted" style="text-align:right;">{{ $r->created_at->format('d M') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state" style="padding:32px 20px;">
                    <p class="text-xs text-muted">No league results recorded yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Record Result form --}}
    <div>
        @if(auth()->user()->hasRole(['admin', 'coach']))
        <div class="card">
            <div class="card-header">
                <span class="card-title">📝 Record League Result</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('league.result') }}">
                    @csrf

                    {{-- Match --}}
                    @php
                        $unrecordedMatches = \App\Models\FootballMatch::upcoming()
                            ->where('type', 'league')
                            ->get();
                    @endphp
                    <div class="form-group">
                        <label class="form-label" for="match_id">Select Match Event <span class="required">*</span></label>
                        <select name="match_id" id="match_id" class="form-control" required>
                            <option value="">-- Choose Match --</option>
                            @foreach($unrecordedMatches as $match)
                            <option value="{{ $match->id }}">{{ $match->formatted_date }} vs {{ $match->opponent }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Teams --}}
                    <div class="form-group">
                        <label class="form-label" for="home_team_id">Home Team <span class="required">*</span></label>
                        <select name="home_team_id" id="home_team_id" class="form-control" required>
                            <option value="">-- Choose Home Team --</option>
                            @foreach($teams as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="away_team_id">Away Team <span class="required">*</span></label>
                        <select name="away_team_id" id="away_team_id" class="form-control" required>
                            <option value="">-- Choose Away Team --</option>
                            @foreach($teams as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Scores --}}
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="home_score">Home Score <span class="required">*</span></label>
                            <input type="number" name="home_score" id="home_score" min="0" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="away_score">Away Score <span class="required">*</span></label>
                            <input type="number" name="away_score" id="away_score" min="0" class="form-control" required>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Match highlights or disciplinary notes..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" style="justify-content:center; margin-top:12px;">
                        Record & Update Standings
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
