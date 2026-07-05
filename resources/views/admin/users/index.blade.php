@extends('layouts.app')
@section('title', 'Club Members')
@section('page-title', 'Club Members')

@section('content')
{{-- Members Stats --}}
<div class="stats-grid" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:18px; margin-bottom:28px;">
    <div class="stat-card emerald" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:20px; display:flex; align-items:center; gap:16px;">
        <div style="font-size:32px; opacity:0.12;">👥</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:24px; font-weight:900; color:var(--emerald-400);">{{ $stats['total'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px;">Total Members</div>
        </div>
    </div>
    <div class="stat-card blue" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:20px; display:flex; align-items:center; gap:16px;">
        <div style="font-size:32px; opacity:0.12;">✓</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:24px; font-weight:900; color:var(--blue-400);">{{ $stats['active'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px;">Active Members</div>
        </div>
    </div>
    <div class="stat-card red" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:20px; display:flex; align-items:center; gap:16px;">
        <div style="font-size:32px; opacity:0.12;">⚠️</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:24px; font-weight:900; color:var(--red-400);">{{ $stats['in_debt'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px;">In Debt</div>
        </div>
    </div>
    <div class="stat-card emerald" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:20px; display:flex; align-items:center; gap:16px;">
        <div style="font-size:32px; opacity:0.12;">💰</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:24px; font-weight:900; color:var(--emerald-400);">{{ $stats['up_to_date'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:1px;">Up to date</div>
        </div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🔍 Search & Filter Members</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            + Add New Member
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap:16px; align-items: end;">
            <div>
                <label class="form-label">Search Query</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, phone, jersey #...">
            </div>
            <div>
                <label class="form-label">Playing Position</label>
                <select name="position" class="form-control">
                    <option value="">All Positions</option>
                    <option value="GK" {{ request('position') === 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                    <option value="DF" {{ request('position') === 'DF' ? 'selected' : '' }}>Defender (DF)</option>
                    <option value="MF" {{ request('position') === 'MF' ? 'selected' : '' }}>Midfielder (MF)</option>
                    <option value="FW" {{ request('position') === 'FW' ? 'selected' : '' }}>Forward (FW)</option>
                </select>
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
        <span class="card-title">👥 Roster</span>
    </div>
    <div class="table-wrap">
        @if($members->count())
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th>Member Profile</th>
                    <th>Position</th>
                    <th>Contact Phone</th>
                    <th>Outstanding Balance</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $m)
                <tr>
                    <td style="text-align: center;">
                        @if($m->jersey_number)
                        <span style="font-family:'Outfit',sans-serif; font-weight:800; font-size:15px; color:var(--gold-400);">{{ $m->jersey_number }}</span>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="{{ $m->avatar_url }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:1.5px solid var(--glass-border);">
                            <div>
                                <strong style="display:block; font-size:14px; color:var(--text-primary);">{{ $m->name }}</strong>
                                <span class="text-xs text-muted">@ @if($m->username){{ $m->username }}@else{{ '—' }}@endif</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($m->position)
                        <span class="badge pos-{{ strtolower($m->position) }}">{{ $m->position }}</span>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-sm font-medium">
                        {{ $m->phone }}
                    </td>
                    <td>
                        @if($m->balance)
                        <strong class="{{ $m->balance->isInCredit() ? 'text-emerald' : 'text-red' }}">
                            KSh {{ number_format(abs($m->balance->balance)) }}
                            {{ $m->balance->isInCredit() ? 'Credit' : 'Owed' }}
                        </strong>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($m->is_active)
                        <span class="badge badge-green">Active</span>
                        @else
                        <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <a href="{{ route('admin.users.show', $m) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">
                            View
                        </a>
                        <a href="{{ route('admin.users.edit', $m) }}" class="btn btn-primary btn-sm" style="padding:4px 8px;">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle', $m) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding:4px 8px; min-width: 75px;">
                                {{ $m->is_active ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <div class="empty-state-title">No members match filters</div>
        </div>
        @endif
    </div>
    @if($members->hasPages())
    <div class="card-footer">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection
