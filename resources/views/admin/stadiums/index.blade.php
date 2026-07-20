@extends('layouts.app')
@section('title', 'Stadiums')
@section('page-title', 'Stadium Management')
@section('breadcrumb') Stadiums @endsection

@section('content')
{{-- Stats Row --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Stadiums</div>
    </div>
    <div class="stat-card">
        <div class="stat-number text-emerald">{{ $stats['active'] }}</div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat-card">
        <div class="stat-number text-muted">{{ $stats['inactive'] }}</div>
        <div class="stat-label">Inactive</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">🏟️ Registered Stadiums</span>
        <a href="{{ route('admin.stadiums.create') }}" class="btn btn-primary btn-sm">+ Add Stadium</a>
    </div>

    {{-- Filters --}}
    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border);">
        <form method="GET" action="{{ route('admin.stadiums.index') }}" class="d-flex" style="gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or location…" class="form-control" style="max-width:260px;">
            <select name="surface" class="form-control" style="max-width:160px;">
                <option value="">All Surfaces</option>
                <option value="grass"      {{ request('surface')==='grass'      ? 'selected' : '' }}>🌿 Natural Grass</option>
                <option value="artificial" {{ request('surface')==='artificial' ? 'selected' : '' }}>🟩 Artificial Turf</option>
                <option value="indoor"     {{ request('surface')==='indoor'     ? 'selected' : '' }}>🏟️ Indoor</option>
            </select>
            <select name="status" class="form-control" style="max-width:140px;">
                <option value="">All Status</option>
                <option value="active"   {{ request('status')==='active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            @if(request()->hasAny(['search','surface','status']))
            <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        @if($stadiums->count())
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Surface</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stadiums as $s)
                <tr>
                    <td>
                        <strong>{{ $s->name }}</strong>
                    </td>
                    <td class="text-sm text-muted">{{ $s->location ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $s->surface_badge_class }}">{{ $s->surface_label }}</span>
                    </td>
                    <td class="text-sm">
                        {{ $s->capacity ? number_format($s->capacity) . ' seats' : '—' }}
                    </td>
                    <td>
                        @if($s->is_active)
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-gray">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div class="d-flex" style="gap:8px; justify-content:flex-end;">
                            <a href="{{ route('admin.stadiums.show', $s) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('admin.stadiums.edit', $s) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.stadiums.destroy', $s) }}" onsubmit="return confirm('Delete {{ addslashes($s->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">🏟️</div>
            <div class="empty-state-title">No stadiums found</div>
            <p class="text-muted text-sm">Add your first stadium to start linking venues to matches.</p>
            <a href="{{ route('admin.stadiums.create') }}" class="btn btn-primary" style="margin-top:12px;">+ Add Stadium</a>
        </div>
        @endif
    </div>

    @if($stadiums->hasPages())
    <div class="card-footer">{{ $stadiums->links() }}</div>
    @endif
</div>
@endsection
