@extends('layouts.app')
@section('title', 'System Users')
@section('page-title', 'User Management')

@section('content')
{{-- Users Stats --}}
<div class="stats-grid" style="display:grid; grid-template-columns:repeat(5, 1fr); gap:12px; margin-bottom:28px;">
    <div class="stat-card" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:16px; display:flex; align-items:center; gap:12px;">
        <div style="font-size:28px; opacity:0.12;">👥</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--text-primary);">{{ $stats['total'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:0.5px; font-size:10px;">Total Users</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:16px; display:flex; align-items:center; gap:12px;">
        <div style="font-size:28px; opacity:0.12;">👑</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--gold-400);">{{ $stats['admins'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:0.5px; font-size:10px;">Admins</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:16px; display:flex; align-items:center; gap:12px;">
        <div style="font-size:28px; opacity:0.12;">📋</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--emerald-400);">{{ $stats['coaches'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:0.5px; font-size:10px;">Coaches</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:16px; display:flex; align-items:center; gap:12px;">
        <div style="font-size:28px; opacity:0.12;">💰</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--blue-400);">{{ $stats['treasurers'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:0.5px; font-size:10px;">Treasurers</div>
        </div>
    </div>
    <div class="stat-card" style="background:var(--navy-800); border:1px solid var(--glass-border); border-radius:var(--radius-lg); padding:16px; display:flex; align-items:center; gap:12px;">
        <div style="font-size:28px; opacity:0.12;">🏃</div>
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; color:var(--text-secondary);">{{ $stats['members'] }}</div>
            <div class="text-xs text-muted" style="text-transform:uppercase; letter-spacing:0.5px; font-size:10px;">Members</div>
        </div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header">
        <span class="card-title">🔍 Search & Filter Users</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            + Add New User
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) 100px; gap:16px; align-items: end;">
            <div>
                <label class="form-label">Search Query</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, phone, username...">
            </div>
            <div>
                <label class="form-label">System Role</label>
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="coach" {{ request('role') === 'coach' ? 'selected' : '' }}>Coach</option>
                    <option value="treasurer" {{ request('role') === 'treasurer' ? 'selected' : '' }}>Treasurer</option>
                    <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member / Player</option>
                </select>
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
                    <th style="width: 50px; text-align: center;">Jersey</th>
                    <th>User Profile</th>
                    <th>Role</th>
                    <th>Position</th>
                    <th>Nationality</th>
                    <th>Assigned Team</th>
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
                        <span class="badge badge-{{ $m->role_color }}">{{ $m->role_label }}</span>
                    </td>
                    <td>
                        @if($m->position)
                        <span class="badge pos-{{ strtolower($m->position) }}">{{ $m->position }}</span>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-sm">
                        {{ $m->nationality ?? '—' }}
                    </td>
                    <td>
                        @if($m->team)
                        <a href="{{ route('admin.teams.show', $m->team) }}" class="badge badge-blue" style="text-decoration:none;">
                            {{ $m->team->short_name }}
                        </a>
                        @else
                        <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($m->role === 'member' && $m->balance)
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
                        <span class="badge badge-red">Suspended</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <div class="d-flex justify-end" style="gap:6px; justify-content: flex-end;">
                            <a href="{{ route('admin.users.show', $m) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">
                                View
                            </a>
                            <a href="{{ route('admin.users.edit', $m) }}" class="btn btn-primary btn-sm" style="padding:4px 8px;">
                                Edit
                            </a>
                            @if($m->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle', $m) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding:4px 8px; min-width: 75px;">
                                    {{ $m->is_active ? 'Suspend' : 'Activate' }}
                                </button>
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
            <div class="empty-state-icon">👥</div>
            <div class="empty-state-title">No users found matching search criteria</div>
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

