@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
/* ── Hero ──────────────────────────────────────────────────────────── */
.dash-hero {
    background: linear-gradient(135deg, var(--navy-700) 0%, var(--navy-800) 55%, rgba(16,185,129,0.07) 100%);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: 28px 32px;
    margin-bottom: 28px;
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    position: relative; overflow: hidden;
}
.dash-hero::before {
    content: ''; position: absolute; right: -60px; top: -60px;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(16,185,129,0.10) 0%, transparent 70%);
    pointer-events: none;
}
.dash-hero::after {
    content: '⚙️'; position: absolute; right: 28px; bottom: -10px;
    font-size: 110px; opacity: 0.04; line-height: 1; pointer-events: none;
}
.hero-greeting { font-size: 11px; color: var(--gold-400); font-weight: 700; letter-spacing: 1.5px; margin-bottom: 4px; text-transform: uppercase; }
.hero-name     { font-family: 'Outfit', sans-serif; font-weight: 900; font-size: 30px; color: var(--text-primary); line-height: 1.1; }
.hero-sub      { font-size: 14px; color: var(--text-secondary); margin-top: 6px; }
.hero-meta     { display: flex; align-items: center; gap: 10px; margin-top: 14px; }

/* ── KPI Grid (7 tiles) ────────────────────────────────────────────── */
.stats-grid-admin {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}
.stats-grid-admin-row2 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}
.stat-tile {
    background: var(--navy-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    padding: 20px 22px;
    position: relative; overflow: hidden;
    transition: var(--transition); cursor: default;
}
.stat-tile:hover { transform: translateY(-3px); box-shadow: 0 10px 36px rgba(0,0,0,0.45); }
.stat-tile-accent { position: absolute; top: 0; left: 0; width: 4px; height: 100%; border-radius: 4px 0 0 4px; }
.stat-tile.emerald .stat-tile-accent { background: linear-gradient(180deg, var(--emerald-400), var(--emerald-600)); }
.stat-tile.gold    .stat-tile-accent { background: linear-gradient(180deg, var(--gold-300), var(--gold-500)); }
.stat-tile.red     .stat-tile-accent { background: linear-gradient(180deg, var(--red-400), var(--red-500)); }
.stat-tile.blue    .stat-tile-accent { background: linear-gradient(180deg, var(--blue-400), var(--blue-500)); }
.stat-tile.purple  .stat-tile-accent { background: linear-gradient(180deg, #A78BFA, #7C3AED); }
.stat-tile.cyan    .stat-tile-accent { background: linear-gradient(180deg, #22D3EE, #0891B2); }
.stat-tile.orange  .stat-tile-accent { background: linear-gradient(180deg, #FB923C, #EA580C); }

.stat-tile-label { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
.stat-tile-value { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 900; line-height: 1; }
.stat-tile.emerald .stat-tile-value { color: var(--emerald-400); }
.stat-tile.gold    .stat-tile-value { color: var(--gold-400); }
.stat-tile.red     .stat-tile-value { color: var(--red-400); }
.stat-tile.blue    .stat-tile-value { color: var(--blue-400); }
.stat-tile.purple  .stat-tile-value { color: #A78BFA; }
.stat-tile.cyan    .stat-tile-value { color: #22D3EE; }
.stat-tile.orange  .stat-tile-value { color: #FB923C; }
.stat-tile-sub  { font-size: 11px; color: var(--text-muted); margin-top: 6px; }
.stat-tile-icon { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); font-size: 32px; opacity: 0.10; }

/* ── Chart Grid (2 x 2) ────────────────────────────────────────────── */
.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-bottom: 28px;
}
.chart-card {
    background: var(--navy-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.chart-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--glass-border);
    display: flex; align-items: center; justify-content: space-between;
}
.chart-card-body { padding: 20px; }
.chart-canvas-wrap { position: relative; height: 200px; }

/* ── Two-column layout ─────────────────────────────────────────────── */
.dash-cols { display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }

/* ── Table ─────────────────────────────────────────────────────────── */
.table-wrap { overflow-x: auto; }
.table-premium { width: 100%; border-collapse: collapse; font-size: 13px; }
.table-premium thead th {
    padding: 12px 16px; font-size: 10px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: var(--text-muted);
    background: var(--navy-750); text-align: left;
}
.table-premium tbody td { padding: 13px 16px; border-bottom: 1px solid var(--glass-border); color: var(--text-primary); vertical-align: middle; }
.table-premium tbody tr:last-child td { border-bottom: none; }
.table-premium tbody tr:hover { background: var(--glass-hover); }
.member-cell { display: flex; align-items: center; gap: 10px; }
.member-avatar-mini { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--glass-border); }

/* ── Financial Widget ───────────────────────────────────────────────── */
.financial-widget { background: var(--navy-800); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 24px; }
.financial-widget-header { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: space-between; }
.financial-widget-body { padding: 20px; }

/* ── Announcement Feed ──────────────────────────────────────────────── */
.announce-feed { display: flex; flex-direction: column; }
.announce-card { padding: 14px 20px; border-bottom: 1px solid var(--glass-border); transition: var(--transition); }
.announce-card:last-child { border-bottom: none; }
.announce-card:hover { background: var(--glass-hover); }

/* ── Attendance Ring ────────────────────────────────────────────────── */
.attend-ring-wrap { display: flex; align-items: center; gap: 16px; }
.attend-ring-svg { flex-shrink: 0; }

/* ── Responsive ─────────────────────────────────────────────────────── */
@media (max-width: 1200px) {
    .stats-grid-admin { grid-template-columns: repeat(2, 1fr); }
    .stats-grid-admin-row2 { grid-template-columns: repeat(3, 1fr); }
    .charts-grid { grid-template-columns: 1fr; }
    .dash-cols { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .stats-grid-admin { grid-template-columns: 1fr 1fr; }
    .stats-grid-admin-row2 { grid-template-columns: 1fr 1fr; }
    .charts-grid { grid-template-columns: 1fr; }
    .dash-hero { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
    .stats-grid-admin, .stats-grid-admin-row2 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────────── --}}
<div class="dash-hero">
    <div>
        <div class="hero-greeting">Administrative Panel</div>
        <div class="hero-name">{{ Str::words(auth()->user()->name, 2, '') }}</div>
        <div class="hero-sub">
            <span class="badge badge-gold" style="font-size:10px;">{{ auth()->user()->role_label }}</span>
            <span style="margin-left:6px;font-size:13px;color:var(--text-muted);">· System Controller</span>
        </div>
        <div class="hero-meta">
            <span style="font-size:12px;color:var(--text-muted);">
                <span style="color:var(--emerald-400);font-weight:600;">{{ $totalMembers }}</span> active members ·
                <span style="color:var(--gold-400);font-weight:600;">{{ $attendanceRate }}%</span> avg attendance rate
            </span>
        </div>
    </div>
    <div style="flex-shrink:0;">
        @php $now = now() @endphp
        <div style="text-align:center;">
            <div style="font-size:10px;color:var(--text-muted);letter-spacing:1.2px;text-transform:uppercase;margin-bottom:4px;">Today</div>
            <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:26px;color:var(--text-primary);">{{ $now->format('d M') }}</div>
            <div style="font-size:12px;color:var(--text-secondary);">{{ $now->format('l') }}</div>
        </div>
    </div>
</div>

{{-- ── Row 1: 4 KPI Tiles ──────────────────────────────────────────── --}}
<div class="stats-grid-admin">
    {{-- Total Members --}}
    <div class="stat-tile gold">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Total Members</div>
        <div class="stat-tile-value">{{ $totalMembers }}</div>
        <div class="stat-tile-sub">Active registered players</div>
        <div class="stat-tile-icon">👥</div>
    </div>
    {{-- Total Money Collected --}}
    <div class="stat-tile emerald">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Money Collected</div>
        <div class="stat-tile-value">KSh {{ number_format($totalContributions) }}</div>
        <div class="stat-tile-sub">All confirmed payments</div>
        <div class="stat-tile-icon">💰</div>
    </div>
    {{-- Outstanding Balances --}}
    <div class="stat-tile red">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Outstanding Balances</div>
        <div class="stat-tile-value">KSh {{ number_format(abs($outstandingBalance)) }}</div>
        <div class="stat-tile-sub">Total member debt owed</div>
        <div class="stat-tile-icon">⚠️</div>
    </div>
    {{-- Upcoming Matches --}}
    <div class="stat-tile blue">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Upcoming Matches</div>
        <div class="stat-tile-value">{{ $upcomingMatches->count() }}</div>
        <div class="stat-tile-sub">Scheduled fixtures</div>
        <div class="stat-tile-icon">🏟️</div>
    </div>
</div>

{{-- ── Row 2: 3 KPI Tiles ──────────────────────────────────────────── --}}
<div class="stats-grid-admin-row2">
    {{-- League Standings --}}
    <div class="stat-tile cyan">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">League Position</div>
        @php $bfcPos = $standings->first(); @endphp
        <div class="stat-tile-value">{{ $bfcPos ? '#'.$standings->keys()->first() + 1 : '—' }}</div>
        <div class="stat-tile-sub">
            @if($bfcPos) {{ $bfcPos->points }} pts · {{ $bfcPos->wins ?? 0 }}W {{ $bfcPos->draws ?? 0 }}D {{ $bfcPos->losses ?? 0 }}L @else No league data @endif
        </div>
        <div class="stat-tile-icon">🏆</div>
    </div>
    {{-- Expenses --}}
    <div class="stat-tile purple">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Total Expenses</div>
        <div class="stat-tile-value">KSh {{ number_format($totalExpenses) }}</div>
        <div class="stat-tile-sub">Approved club expenses</div>
        <div class="stat-tile-icon">💸</div>
    </div>
    {{-- Attendance Rate --}}
    <div class="stat-tile orange">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Attendance Rate</div>
        <div class="stat-tile-value">{{ $attendanceRate }}%</div>
        <div class="stat-tile-sub">Across {{ $completedMatches }} completed matches</div>
        <div class="stat-tile-icon">📊</div>
    </div>
</div>

{{-- ── Charts Grid (2 × 2) ─────────────────────────────────────────── --}}
<div class="charts-grid">

    {{-- Chart 1: Monthly Income --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="card-title" style="font-size:14px;">📈 Monthly Income</span>
            <span class="badge badge-green" style="font-size:10px;">Last 6 Months</span>
        </div>
        <div class="chart-card-body">
            <div class="chart-canvas-wrap">
                <canvas id="chartIncome"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 2: Monthly Expenses --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="card-title" style="font-size:14px;">📉 Monthly Expenses</span>
            <span class="badge badge-red" style="font-size:10px;">Last 6 Months</span>
        </div>
        <div class="chart-card-body">
            <div class="chart-canvas-wrap">
                <canvas id="chartExpenses"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 3: Attendance Graph --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="card-title" style="font-size:14px;">📅 Attendance Graph</span>
            <span class="badge badge-blue" style="font-size:10px;">Member Turn-outs</span>
        </div>
        <div class="chart-card-body">
            <div class="chart-canvas-wrap">
                <canvas id="chartAttendance"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart 4: League Performance --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <span class="card-title" style="font-size:14px;">🏆 League Performance</span>
            <span class="badge badge-yellow" style="font-size:10px;">Season 2025/26</span>
        </div>
        <div class="chart-card-body">
            <div class="chart-canvas-wrap" style="display:flex;align-items:center;justify-content:center;">
                <div style="position:relative;width:200px;height:200px;">
                    <canvas id="chartLeague"></canvas>
                </div>
                <div style="margin-left:24px;display:flex;flex-direction:column;gap:10px;">
                    @php $bfc = $standings->first(); @endphp
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:12px;height:12px;border-radius:2px;background:rgba(16,185,129,0.8);flex-shrink:0;"></div>
                        <span class="text-sm">Wins <strong class="text-emerald">{{ $bfc?->wins ?? 0 }}</strong></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:12px;height:12px;border-radius:2px;background:rgba(245,158,11,0.8);flex-shrink:0;"></div>
                        <span class="text-sm">Draws <strong style="color:var(--gold-400);">{{ $bfc?->draws ?? 0 }}</strong></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:12px;height:12px;border-radius:2px;background:rgba(239,68,68,0.8);flex-shrink:0;"></div>
                        <span class="text-sm">Losses <strong class="text-red">{{ $bfc?->losses ?? 0 }}</strong></span>
                    </div>
                    @if($bfc)
                    <div style="margin-top:6px;padding-top:10px;border-top:1px solid var(--glass-border);">
                        <div class="text-xs text-muted">Points</div>
                        <div style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:900;color:var(--emerald-400);">{{ $bfc->points }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Two-Column Body ─────────────────────────────────────────────── --}}
<div class="dash-cols">

    {{-- LEFT COLUMN --}}
    <div>
        {{-- Upcoming Matches --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">🏟️ Upcoming Matches</span>
                <a href="{{ route('matches.create') }}" class="btn btn-primary btn-sm">+ New Match</a>
            </div>
            <div class="table-wrap">
                @if($upcomingMatches->count())
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Date / Kickoff</th>
                            <th>Type</th>
                            <th>Venue</th>
                            <th>Deadline</th>
                            <th style="text-align:center;">✅ Avail</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($upcomingMatches as $match)
                    <tr>
                        <td>
                            <strong>{{ $match->formatted_date }}</strong><br>
                            <span class="text-xs text-muted">{{ $match->match_time }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $match->type === 'league' ? 'badge-blue' : 'badge-gray' }}">
                                {{ ucfirst($match->type) }}
                            </span>
                        </td>
                        <td class="text-sm">{{ $match->venue }}</td>
                        <td class="text-xs text-muted">{{ $match->deadline->format('d M, H:i') }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-green" style="font-family:'Outfit',sans-serif;font-size:12px;">
                                {{ $match->availabilities()->where('status', 'available')->count() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $match->status_badge['class'] }}">
                                {{ $match->status_badge['label'] }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('matches.show', $match) }}" class="btn btn-secondary btn-sm btn-icon">👁️</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📅</div>
                    <div class="empty-state-title">No upcoming matches</div>
                    <p class="text-xs text-muted">Create a new match to begin tracking player availability.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">💳 Recent Payments</span>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                @if($recentPayments->count())
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>M-Pesa Code</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($recentPayments as $p)
                    <tr>
                        <td>
                            <div class="member-cell">
                                <img src="{{ $p->user?->avatar_url }}" class="member-avatar-mini" alt="">
                                <span>{{ $p->user?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td><strong class="text-emerald">KSh {{ number_format($p->amount) }}</strong></td>
                        <td><span class="text-xs">{{ $p->getTypeLabel() }}</span></td>
                        <td class="text-xs" style="font-family:monospace;letter-spacing:0.5px;">{{ $p->mpesa_code ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $p->getStatusBadge()['class'] }}">{{ $p->getStatusBadge()['label'] }}</span>
                        </td>
                        <td class="text-xs text-muted">{{ $p->created_at->format('d M, H:i') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">💳</div>
                    <div class="empty-state-title">No payments recorded</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Outstanding Balances Table --}}
        @if($membersWithDebt->count())
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="color:var(--red-400);">🔴 Outstanding Balances</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">All Members</a>
            </div>
            <div class="table-wrap">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Position</th>
                            <th>Debt Owed</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($membersWithDebt as $bal)
                    <tr>
                        <td>
                            <div class="member-cell">
                                <img src="{{ $bal->user?->avatar_url }}" class="member-avatar-mini" alt="">
                                <strong>{{ $bal->user?->name }}</strong>
                            </div>
                        </td>
                        <td>
                            @if($bal->user?->position)
                            <span class="badge pos-{{ strtolower($bal->user?->position) }}">{{ $bal->user?->position }}</span>
                            @else
                            <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                        <td><strong class="text-red">KSh {{ number_format(abs($bal->balance)) }}</strong></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT COLUMN --}}
    <div>
        {{-- Financial Summary Widget --}}
        <div class="financial-widget" style="margin-bottom:24px;">
            <div class="financial-widget-header">
                <span class="card-title">📊 Financial Summary</span>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">Expenses</a>
            </div>
            <div class="financial-widget-body">
                @php
                    $net = $totalContributions - $totalExpenses;
                    $pct = $totalContributions > 0 ? min(100, ($totalExpenses / $totalContributions) * 100) : 0;
                @endphp
                <div class="d-flex justify-between mb-2">
                    <span class="text-sm text-secondary">Total Contributions</span>
                    <strong class="text-emerald">KSh {{ number_format($totalContributions) }}</strong>
                </div>
                <div class="d-flex justify-between mb-2">
                    <span class="text-sm text-secondary">Total Expenses</span>
                    <strong class="text-red">KSh {{ number_format($totalExpenses) }}</strong>
                </div>
                <hr class="divider">
                <div class="d-flex justify-between" style="margin-bottom:16px;">
                    <span class="text-sm font-bold">Net Balance</span>
                    <strong class="{{ $net >= 0 ? 'text-emerald' : 'text-red' }}" style="font-size:16px;">
                        KSh {{ number_format(abs($net)) }} {{ $net >= 0 ? '✓' : '⚠️' }}
                    </strong>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar {{ $pct > 80 ? 'red' : 'emerald' }}" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs text-muted" style="margin-top:10px;">
                    {{ round($pct) }}% of contributions spent on expenses.
                </p>
            </div>
        </div>

        {{-- Attendance Rate Ring --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">📅 Attendance Rate</span>
                <span class="badge badge-{{ $attendanceRate >= 70 ? 'green' : ($attendanceRate >= 40 ? 'yellow' : 'red') }}" style="font-size:10px;">
                    {{ $attendanceRate >= 70 ? 'Excellent' : ($attendanceRate >= 40 ? 'Average' : 'Low') }}
                </span>
            </div>
            <div class="card-body" style="padding:20px;">
                @php
                    $r    = 44;
                    $circ = 2 * M_PI * $r;
                    $dash = ($attendanceRate / 100) * $circ;
                @endphp
                <div class="attend-ring-wrap">
                    <div style="position:relative;width:106px;height:106px;flex-shrink:0;">
                        <svg width="106" height="106" viewBox="0 0 106 106" style="transform:rotate(-90deg);">
                            <circle cx="53" cy="53" r="{{ $r }}" fill="none" stroke="var(--navy-750)" stroke-width="9"/>
                            <circle cx="53" cy="53" r="{{ $r }}" fill="none"
                                stroke="{{ $attendanceRate >= 70 ? 'var(--emerald-400)' : ($attendanceRate >= 40 ? 'var(--gold-400)' : 'var(--red-400)') }}"
                                stroke-width="9" stroke-linecap="round"
                                stroke-dasharray="{{ number_format($dash, 2) }} {{ number_format($circ, 2) }}"
                                style="transition: stroke-dasharray 1.2s ease;"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:22px;color:var(--text-primary);">{{ $attendanceRate }}%</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-bold" style="margin-bottom:6px;">Member Attendance</div>
                        <div class="text-xs text-muted" style="margin-bottom:4px;">Across <strong>{{ $completedMatches }}</strong> completed matches</div>
                        <div class="text-xs text-muted">Target: <strong style="color:var(--emerald-400);">70%+</strong></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- League Standings Card --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">🏆 League Standings</span>
                <a href="{{ route('league.standings') }}" class="btn btn-secondary btn-sm">Full Table</a>
            </div>
            @if($standings->count())
            <table class="table-premium" style="font-size:12px;">
                <thead>
                    <tr>
                        <th style="width:30px;">#</th>
                        <th>Team</th>
                        <th style="text-align:center;">P</th>
                        <th style="text-align:center;">W</th>
                        <th style="text-align:center;">D</th>
                        <th style="text-align:center;">L</th>
                        <th style="text-align:center;">Pts</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($standings as $i => $s)
                <tr style="{{ $i === 0 ? 'background:rgba(16,185,129,0.04);' : '' }}">
                    <td style="text-align:center;font-weight:bold;color:{{ $i === 0 ? 'var(--gold-400)' : 'var(--text-muted)' }};">{{ $i + 1 }}</td>
                    <td><strong>{{ $s->team?->short_name ?? 'Team' }}</strong></td>
                    <td style="text-align:center;color:var(--text-secondary);">{{ $s->played ?? ($s->wins + $s->draws + $s->losses) }}</td>
                    <td style="text-align:center;color:var(--text-secondary);">{{ $s->wins }}</td>
                    <td style="text-align:center;color:var(--text-secondary);">{{ $s->draws }}</td>
                    <td style="text-align:center;color:var(--text-secondary);">{{ $s->losses }}</td>
                    <td style="text-align:center;font-weight:bold;color:var(--emerald-400);">{{ $s->points }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state" style="padding:20px;">
                <div class="empty-state-icon">🏆</div>
                <p class="text-xs text-muted">No league records found.</p>
            </div>
            @endif
        </div>

        {{-- Announcements --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📢 Announcements</span>
                <a href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">Post</a>
            </div>
            <div class="announce-feed">
                @forelse($recentAnnouncements as $ann)
                <div class="announce-card">
                    <div class="d-flex justify-between align-center mb-1">
                        <span class="badge {{ $ann->getTypeBadgeClass() }}" style="font-size:9px;">{{ $ann->getTypeLabel() }}</span>
                        <span class="text-xs text-muted">{{ $ann->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="font-bold text-sm" style="margin-top:4px;">{{ $ann->title }}</div>
                    <div class="text-xs text-secondary" style="margin-top:4px;line-height:1.4;">{{ Str::limit($ann->body, 90) }}</div>
                </div>
                @empty
                <div class="empty-state" style="padding:30px;">
                    <p class="text-xs text-muted">No announcements posted yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const months   = {!! $chartMonths !!};
    const income   = {!! $chartIncome !!};
    const expenses = {!! $chartExpenses !!};
    const attend   = {!! $chartAttendance !!};
    const league   = {!! $chartLeague !!};

    Chart.defaults.color = 'rgba(148,163,184,0.85)';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 11;

    const gridColor = 'rgba(255,255,255,0.05)';

    const baseScaleOpts = {
        grid:  { color: gridColor, drawBorder: false },
        ticks: { padding: 6 },
    };

    // ── Chart 1: Monthly Income ──────────────────
    new Chart(document.getElementById('chartIncome'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Income (KSh)',
                data: income,
                backgroundColor: 'rgba(16,185,129,0.25)',
                borderColor: 'rgba(16,185,129,0.9)',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(16,185,129,0.45)',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' KSh ' + ctx.parsed.y.toLocaleString() } } },
            scales: {
                x: { ...baseScaleOpts },
                y: { ...baseScaleOpts, beginAtZero: true, ticks: { ...baseScaleOpts.ticks, callback: v => 'KSh ' + (v/1000).toFixed(0) + 'k' } }
            }
        }
    });

    // ── Chart 2: Monthly Expenses ────────────────
    new Chart(document.getElementById('chartExpenses'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Expenses (KSh)',
                data: expenses,
                backgroundColor: 'rgba(239,68,68,0.20)',
                borderColor: 'rgba(239,68,68,0.85)',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(239,68,68,0.38)',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' KSh ' + ctx.parsed.y.toLocaleString() } } },
            scales: {
                x: { ...baseScaleOpts },
                y: { ...baseScaleOpts, beginAtZero: true, ticks: { ...baseScaleOpts.ticks, callback: v => 'KSh ' + (v/1000).toFixed(0) + 'k' } }
            }
        }
    });

    // ── Chart 3: Attendance Graph ────────────────
    new Chart(document.getElementById('chartAttendance'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Member Turn-outs',
                data: attend,
                borderColor: 'rgba(96,165,250,0.9)',
                backgroundColor: 'rgba(96,165,250,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: 'rgba(96,165,250,1)',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ...baseScaleOpts },
                y: { ...baseScaleOpts, beginAtZero: true }
            }
        }
    });

    // ── Chart 4: League Performance (Doughnut) ───
    const leagueData = league.data;
    const hasLeague  = leagueData.some(v => v > 0);
    new Chart(document.getElementById('chartLeague'), {
        type: 'doughnut',
        data: {
            labels: league.labels,
            datasets: [{
                data: hasLeague ? leagueData : [1, 1, 1],
                backgroundColor: [
                    'rgba(16,185,129,0.75)',
                    'rgba(245,158,11,0.75)',
                    'rgba(239,68,68,0.75)',
                ],
                borderColor: [
                    'rgba(16,185,129,1)',
                    'rgba(245,158,11,1)',
                    'rgba(239,68,68,1)',
                ],
                borderWidth: 2,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasLeague,
                    callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed }
                }
            }
        }
    });
})();
</script>
@endpush
