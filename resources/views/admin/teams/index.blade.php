@extends('layouts.app')
@section('title', 'League Teams')
@section('page-title', 'League Teams')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Admin</a> / Teams
@endsection

@section('content')
<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🔍 Search & Filter Teams</span>
        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm">
            + Add New Team
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.teams.index') }}" style="display:grid; grid-template-columns: 1fr 180px 100px; gap:16px; align-items: end;">
            <div>
                <label class="form-label">Search Query</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or short code...">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">🛡️ Registered League Teams</span>
    </div>
    <div class="table-wrap">
        @if($teams->count())
        <table>
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Badge Color</th>
                    <th>Team Name</th>
                    <th>Short Code</th>
                    <th>Kit Color</th>
                    <th style="text-align: center;">Active Players</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teams as $t)
                <tr>
                    <td style="text-align: center;">
                        <span style="display: inline-block; width: 24px; height: 24px; border-radius: 50%; background-color: {{ $t->color }}; border: 1.5px solid var(--glass-border);"></span>
                    </td>
                    <td>
                        <strong style="font-size:14px; color:var(--text-primary);">{{ $t->name }}</strong>
                    </td>
                    <td>
                        <span class="badge badge-blue">{{ $t->short_name }}</span>
                    </td>
                    <td>
                        @if($t->kit_color)
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="display: inline-block; width: 14px; height: 14px; border-radius: 2px; background-color: {{ $t->kit_color }}; border: 1px solid var(--glass-border);"></span>
                            <span class="text-xs text-muted">{{ strtoupper($t->kit_color) }}</span>
                        </div>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <strong class="text-emerald" style="font-size: 14px;">{{ $t->players_count ?? $t->players()->count() }}</strong>
                    </td>
                    <td>
                        @if($t->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <a href="{{ route('admin.teams.show', $t) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">
                            View Roster
                        </a>
                        <a href="{{ route('admin.teams.edit', $t) }}" class="btn btn-primary btn-sm" style="padding:4px 8px;">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.teams.destroy', $t) }}" onsubmit="return confirm('Are you sure you want to delete this team? Player mappings will be nullified.');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🛡️</div>
            <div class="empty-state-title">No teams registered</div>
        </div>
        @endif
    </div>
    @if($teams->hasPages())
    <div class="card-footer">
        {{ $teams->links() }}
    </div>
    @endif
</div>
@endsection
