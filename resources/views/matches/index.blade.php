@extends('layouts.app')
@section('title', 'Matches')
@section('page-title', 'Matches')

@section('content')
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
                    <th>Type</th>
                    <th>Venue</th>
                    <th>Match Fee</th>
                    <th>Deadline</th>
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
                    <td>
                        <span class="badge {{ $m->type==='league'?'badge-blue':'badge-gray' }}">{{ ucfirst($m->type) }}</span>
                    </td>
                    <td class="text-sm">{{ $m->venue }}</td>
                    <td class="text-sm text-gold">KSh {{ number_format($m->match_fee) }}</td>
                    <td class="text-xs text-muted">{{ $m->deadline->format('d M, H:i') }}</td>
                    <td>
                        <span class="badge {{ $m->status_badge['class'] }}">{{ $m->status_badge['label'] }}</span>
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('matches.show', $m) }}" class="btn btn-secondary btn-sm">View Details</a>
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
    <div class="card-footer">
        {{ $upcoming->links() }}
    </div>
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
                    <td>
                        <span class="badge {{ $m->type==='league'?'badge-blue':'badge-gray' }}">{{ ucfirst($m->type) }}</span>
                    </td>
                    <td class="text-sm">{{ $m->venue }}</td>
                    <td>
                        @if($m->home_score !== null && $m->away_score !== null)
                        <strong class="text-emerald">{{ $m->home_score }} - {{ $m->away_score }}</strong> vs {{ $m->opponent }}
                        @else
                        <span class="text-xs text-muted">No result recorded</span>
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
            <p class="text-xs text-muted">No past matches recorded in the system.</p>
        </div>
        @endif
    </div>
</div>
@endsection
