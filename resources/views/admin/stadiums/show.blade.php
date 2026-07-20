@extends('layouts.app')
@section('title', $stadium->name)
@section('page-title', $stadium->name)
@section('breadcrumb')
<a href="{{ route('admin.stadiums.index') }}">Stadiums</a> / {{ $stadium->name }}
@endsection

@section('content')
<div class="grid" style="grid-template-columns:1fr 2fr; gap:24px; align-items:start;">

    {{-- Stadium Info Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🏟️ Stadium Details</span>
            <a href="{{ route('admin.stadiums.edit', $stadium) }}" class="btn btn-secondary btn-sm">Edit</a>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:16px;">
            <div style="text-align:center; padding:24px 0;">
                <div style="font-size:64px; line-height:1;">🏟️</div>
                <h2 style="margin:12px 0 4px; font-size:1.4rem;">{{ $stadium->name }}</h2>
                @if($stadium->is_active)
                    <span class="badge badge-green">Active</span>
                @else
                    <span class="badge badge-gray">Inactive</span>
                @endif
            </div>

            <div style="border-top:1px solid var(--border); padding-top:16px; display:flex; flex-direction:column; gap:10px;">
                <div class="d-flex justify-between text-sm">
                    <span class="text-muted">Location</span>
                    <span>{{ $stadium->location ?? '—' }}</span>
                </div>
                <div class="d-flex justify-between text-sm">
                    <span class="text-muted">Surface</span>
                    <span class="badge {{ $stadium->surface_badge_class }}">{{ $stadium->surface_label }}</span>
                </div>
                <div class="d-flex justify-between text-sm">
                    <span class="text-muted">Capacity</span>
                    <span>{{ $stadium->capacity ? number_format($stadium->capacity) . ' seats' : '—' }}</span>
                </div>
                @if($stadium->notes)
                <div style="margin-top:8px; padding:12px; background:var(--surface-2); border-radius:8px; font-size:0.85rem; color:var(--text-muted);">
                    {{ $stadium->notes }}
                </div>
                @endif
            </div>

            <div style="border-top:1px solid var(--border); padding-top:12px;">
                <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium) }}"
                    onsubmit="return confirm('Are you sure you want to delete this stadium?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">🗑 Delete Stadium</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Recent Matches --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚽ Matches at This Venue</span>
            <span class="badge badge-blue">{{ $recentMatches->count() }} matches</span>
        </div>
        <div class="table-wrap">
            @if($recentMatches->count())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opponent</th>
                        <th>Type</th>
                        <th>Result</th>
                        <th style="text-align:right;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentMatches as $m)
                    <tr>
                        <td>
                            <strong>{{ $m->formatted_date }}</strong><br>
                            <span class="text-xs text-muted">{{ $m->match_time }}</span>
                        </td>
                        <td class="text-sm">{{ $m->opponent }}</td>
                        <td>
                            <span class="badge {{ $m->type==='league' ? 'badge-blue' : 'badge-gray' }}">{{ ucfirst($m->type) }}</span>
                        </td>
                        <td>
                            @if($m->home_score !== null && $m->away_score !== null)
                                <strong class="text-emerald">{{ $m->home_score }} – {{ $m->away_score }}</strong>
                            @else
                                <span class="text-xs text-muted">{{ ucfirst($m->status) }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('matches.show', $m) }}" class="btn btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state" style="padding:40px;">
                <div class="empty-state-icon">⚽</div>
                <div class="empty-state-title">No matches recorded at this venue yet</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
