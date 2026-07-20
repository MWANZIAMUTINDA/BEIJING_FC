@extends('layouts.app')
@section('title', 'Matches')
@section('page-title', 'Matches & Fixtures')

@section('content')
{{-- Search & Filters --}}
<div class="card mb-6" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" action="{{ route('matches.index') }}" class="d-flex" style="gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search opponent, venue…" class="form-control" style="max-width:240px;">
            <select name="type" class="form-control" style="max-width:150px;">
                <option value="">All Types</option>
                <option value="league"   {{ request('type')==='league'   ? 'selected' : '' }}>League</option>
                <option value="friendly" {{ request('type')==='friendly' ? 'selected' : '' }}>Friendly</option>
            </select>
            <select name="status" class="form-control" style="max-width:150px;">
                <option value="">All Status</option>
                <option value="upcoming"  {{ request('status')==='upcoming'  ? 'selected' : '' }}>Upcoming</option>
                <option value="open"      {{ request('status')==='open'      ? 'selected' : '' }}>Open</option>
                <option value="locked"    {{ request('status')==='locked'    ? 'selected' : '' }}>Locked</option>
                <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('matches.index') }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🏟️ Scheduled Fixtures</span>
        @if(auth()->user()->hasRole(['admin', 'coach']))
        <a href="{{ route('matches.create') }}" class="btn btn-primary btn-sm">+ New Match</a>
        @endif
    </div>
    <div class="table-wrap">
        @if($upcoming->count())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Opponent</th>
                    <th>Type</th>
                    <th>Venue</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcoming as $m)
                <tr>
                    <td>
                        <strong>{{ $m->formatted_date }}</strong><br>
                        <span class="text-xs text-muted">{{ $m->match_time }}</span>
                    </td>
                    <td class="text-sm"><strong>{{ $m->opponent }}</strong></td>
                    <td>
                        <span class="badge {{ $m->type==='league'?'badge-blue':'badge-gray' }}">{{ ucfirst($m->type) }}</span>
                    </td>
                    <td class="text-sm text-muted">{{ Str::limit($m->venue, 30) }}</td>
                    <td class="text-sm text-gold">KSh {{ number_format($m->match_fee) }}</td>
                    <td>
                        <span class="badge {{ $m->status_badge['class'] }}">{{ $m->status_badge['label'] }}</span>
                    </td>
                    <td style="text-align:right;">
                        <div class="d-flex" style="gap:6px; justify-content:flex-end;">
                            <a href="{{ route('matches.show', $m) }}" class="btn btn-secondary btn-sm">View</a>
                            @if(auth()->user()->hasRole(['admin','coach']))
                            <a href="{{ route('matches.edit', $m) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('matches.destroy', $m) }}"
                                onsubmit="return confirm('Delete this match?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">⚽</div>
            <div class="empty-state-title">No upcoming matches scheduled</div>
        </div>
        @endif
    </div>
    @if($upcoming->hasPages())
    <div class="card-footer">{{ $upcoming->links() }}</div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">📜 Past Matches & Results</span>
    </div>
    <div class="table-wrap">
        @if($past->count())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Opponent</th>
                    <th>Type</th>
                    <th>Venue</th>
                    <th>Result</th>
                    <th style="text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($past as $m)
                <tr>
                    <td>
                        <strong>{{ $m->formatted_date }}</strong><br>
                        <span class="text-xs text-muted">{{ $m->match_time }}</span>
                    </td>
                    <td class="text-sm">{{ $m->opponent }}</td>
                    <td>
                        <span class="badge {{ $m->type==='league'?'badge-blue':'badge-gray' }}">{{ ucfirst($m->type) }}</span>
                    </td>
                    <td class="text-sm text-muted">{{ Str::limit($m->venue, 25) }}</td>
                    <td>
                        @if($m->home_score !== null && $m->away_score !== null)
                        <strong class="text-emerald">{{ $m->home_score }} – {{ $m->away_score }}</strong>
                        @else
                        <span class="text-xs text-muted">No result</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('matches.show', $m) }}" class="btn btn-secondary btn-sm">Stats</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="padding:40px 20px;">
            <p class="text-xs text-muted">No past matches recorded.</p>
        </div>
        @endif
    </div>
    @if($past->hasPages())
    <div class="card-footer">{{ $past->links() }}</div>
    @endif
</div>
@endsection

</div>
@endsection
