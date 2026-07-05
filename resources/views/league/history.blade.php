@extends('layouts.app')
@section('title', 'League Match History')
@section('page-title', 'Match History')
@section('breadcrumb')
<a href="{{ route('league.standings') }}">League</a> / History
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">📜 Complete League Fixtures Log</span>
    </div>
    <div class="table-wrap">
        @if($results->count())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Fixture</th>
                    <th>Scoreline</th>
                    <th>Result Outcome</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $r)
                <tr>
                    <td>
                        <strong>{{ $r->created_at->format('d M Y') }}</strong><br>
                        <span class="text-xs text-muted">{{ $r->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <strong class="text-emerald">{{ $r->homeTeam?->name }}</strong>
                        <span class="text-muted">vs</span>
                        <strong>{{ $r->awayTeam?->name }}</strong>
                    </td>
                    <td>
                        <strong style="font-size:16px;">{{ $r->home_score }} - {{ $r->away_score }}</strong>
                    </td>
                    <td>
                        @if($r->result === 'home_win')
                        <span class="badge badge-green">Home Win</span>
                        @elseif($r->result === 'away_win')
                        <span class="badge badge-blue">Away Win</span>
                        @else
                        <span class="badge badge-yellow">Draw</span>
                        @endif
                    </td>
                    <td class="text-sm text-secondary">
                        {{ $r->recorder?->name ?? 'System' }}
                    </td>
                    <td class="text-xs text-muted" style="max-width: 200px; white-space: normal;">
                        {{ $r->notes ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div class="empty-state-title">No historical logs found</div>
        </div>
        @endif
    </div>
    @if($results->hasPages())
    <div class="card-footer">
        {{ $results->links() }}
    </div>
    @endif
</div>
@endsection
