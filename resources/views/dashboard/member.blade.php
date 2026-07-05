@extends('layouts.app')
@section('title', 'My Dashboard')
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
    content: '⚽'; position: absolute; right: 28px; bottom: -10px;
    font-size: 110px; opacity: 0.06; line-height: 1; pointer-events: none;
}
.hero-greeting { font-size: 13px; color: var(--emerald-400); font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px; text-transform: uppercase; }
.hero-name     { font-family: 'Outfit', sans-serif; font-weight: 900; font-size: 30px; color: var(--text-primary); line-height: 1.1; }
.hero-sub      { font-size: 14px; color: var(--text-secondary); margin-top: 6px; }
.hero-meta     { display: flex; align-items: center; gap: 10px; margin-top: 14px; }

/* ── 5 Stat Cards Row ──────────────────────────────────────────────── */
.stats-row-5 {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
.stat-tile {
    background: var(--navy-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    padding: 18px 20px;
    position: relative; overflow: hidden;
    transition: var(--transition); cursor: default;
}
.stat-tile:hover { transform: translateY(-3px); box-shadow: 0 10px 36px rgba(0,0,0,0.45); }
.stat-tile-accent { position: absolute; top: 0; left: 0; width: 4px; height: 100%; border-radius: 4px 0 0 4px; }
.stat-tile.emerald .stat-tile-accent { background: linear-gradient(180deg, var(--emerald-400), var(--emerald-600)); }
.stat-tile.gold    .stat-tile-accent { background: linear-gradient(180deg, var(--gold-300), var(--gold-500)); }
.stat-tile.blue    .stat-tile-accent { background: linear-gradient(180deg, var(--blue-400), var(--blue-500)); }
.stat-tile.purple  .stat-tile-accent { background: linear-gradient(180deg, #A78BFA, #7C3AED); }
.stat-tile.orange  .stat-tile-accent { background: linear-gradient(180deg, #FB923C, #EA580C); }
.stat-tile.red     .stat-tile-accent { background: linear-gradient(180deg, var(--red-400), var(--red-500)); }
.stat-tile.cyan    .stat-tile-accent { background: linear-gradient(180deg, #22D3EE, #0891B2); }
.stat-tile-label { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
.stat-tile-value { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 900; line-height: 1; }
.stat-tile.emerald .stat-tile-value { color: var(--emerald-400); }
.stat-tile.gold    .stat-tile-value { color: var(--gold-400); }
.stat-tile.blue    .stat-tile-value { color: var(--blue-400); }
.stat-tile.purple  .stat-tile-value { color: #A78BFA; }
.stat-tile.orange  .stat-tile-value { color: #FB923C; }
.stat-tile.red     .stat-tile-value { color: var(--red-400); }
.stat-tile.cyan    .stat-tile-value { color: #22D3EE; }
.stat-tile-sub  { font-size: 10px; color: var(--text-muted); margin-top: 6px; }
.stat-tile-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 28px; opacity: 0.10; }

/* ── Two-column layout ─────────────────────────────────────────────── */
.dash-cols { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }

/* ── Next Match Hero Card ────────────────────────────────────────────── */
.match-hero-card {
    background: linear-gradient(135deg, var(--navy-700) 0%, var(--navy-800) 100%);
    border: 1px solid rgba(16,185,129,0.2);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
}
.match-hero-banner {
    background: linear-gradient(90deg, rgba(16,185,129,0.13) 0%, transparent 100%);
    padding: 14px 22px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid rgba(16,185,129,0.15);
}
.match-hero-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 15px; color: var(--emerald-400); display: flex; align-items: center; gap: 8px; }
.match-details-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; border-bottom: 1px solid var(--glass-border); }
.match-detail-cell { padding: 16px 22px; border-right: 1px solid var(--glass-border); }
.match-detail-cell:last-child { border-right: none; }
.match-detail-label { font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; }
.match-detail-val   { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 15px; color: var(--text-primary); }
.match-detail-val.emerald { color: var(--emerald-400); }
.match-detail-val.gold    { color: var(--gold-400); }
.match-avail-zone   { padding: 16px 22px; }
.match-avail-label  { font-size: 12px; color: var(--text-muted); margin-bottom: 12px; font-weight: 500; }
.avail-btns { display: flex; gap: 8px; flex-wrap: wrap; }
.avail-btn {
    flex: 1; padding: 10px 8px;
    border-radius: var(--radius-sm); border: 1px solid var(--glass-border);
    background: var(--navy-750); color: var(--text-secondary);
    font-size: 13px; font-weight: 600; cursor: pointer; transition: var(--transition);
    text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; min-width: 90px;
}
.avail-btn:hover { border-color: rgba(255,255,255,0.2); color: var(--text-primary); transform: translateY(-1px); }
.avail-btn.active-avail   { background: rgba(16,185,129,0.15);  border-color: rgba(16,185,129,0.4);  color: var(--emerald-400); }
.avail-btn.active-unavail { background: rgba(239,68,68,0.15);   border-color: rgba(239,68,68,0.4);   color: var(--red-400); }
.avail-btn.active-maybe   { background: rgba(245,158,11,0.15);  border-color: rgba(245,158,11,0.4);  color: var(--gold-400); }
.current-status-row { display: flex; align-items: center; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--glass-border); font-size: 12px; color: var(--text-muted); }

/* ── Upcoming Matches List ──────────────────────────────────────────── */
.match-list { display: flex; flex-direction: column; gap: 10px; padding: 16px; }
.match-item { display: flex; align-items: center; gap: 14px; background: var(--navy-750); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 14px 16px; transition: var(--transition); text-decoration: none; }
.match-item:hover { border-color: rgba(255,255,255,0.15); transform: translateX(2px); background: var(--glass-hover); }
.match-date-box { width: 46px; height: 46px; background: var(--navy-800); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0; }
.match-date-day { font-family: 'Outfit', sans-serif; font-weight: 900; font-size: 18px; line-height: 1; color: var(--text-primary); }
.match-date-mon { font-size: 9px; font-weight: 700; text-transform: uppercase; color: var(--emerald-400); letter-spacing: 1px; }
.match-item-info { flex: 1; min-width: 0; }
.match-item-venue { font-weight: 600; font-size: 13px; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.match-item-time  { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.match-item-fee   { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 13px; color: var(--gold-400); white-space: nowrap; }

/* ── Personal Balance Card ───────────────────────────────────────────── */
.balance-card { background: var(--navy-800); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 20px; }
.balance-card-header { padding: 14px 20px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: space-between; }
.balance-card-body { padding: 20px; }
.balance-main { display: flex; align-items: center; gap: 18px; margin-bottom: 18px; }
.balance-ring-wrap { position: relative; width: 88px; height: 88px; flex-shrink: 0; }
.balance-ring-wrap svg { transform: rotate(-90deg); }
.balance-ring-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.balance-amount { font-family: 'Outfit', sans-serif; font-weight: 900; font-size: 26px; line-height: 1; margin-bottom: 4px; }
.balance-amount.credit  { color: var(--emerald-400); }
.balance-amount.overdue { color: var(--red-400); }
.balance-desc { font-size: 12px; color: var(--text-muted); }
.balance-rows { display: flex; flex-direction: column; gap: 10px; }
.balance-row  { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
.balance-row-label { color: var(--text-muted); }

/* ── Payment Feed ────────────────────────────────────────────────────── */
.payment-feed { display: flex; flex-direction: column; }
.payment-entry { display: flex; align-items: center; gap: 12px; padding: 13px 20px; border-bottom: 1px solid var(--glass-border); transition: var(--transition); }
.payment-entry:last-child { border-bottom: none; }
.payment-entry:hover { background: var(--glass-hover); }
.payment-dot { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; }
.payment-dot.confirmed { background: rgba(16,185,129,0.12); }
.payment-dot.pending   { background: rgba(245,158,11,0.12); }
.payment-dot.failed    { background: rgba(239,68,68,0.12); }
.payment-info { flex: 1; min-width: 0; }
.payment-type   { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.payment-date   { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
.payment-right  { text-align: right; flex-shrink: 0; }
.payment-amount { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 13px; color: var(--emerald-400); }
.payment-status { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }
.payment-status.confirmed { color: var(--emerald-400); }
.payment-status.pending   { color: var(--gold-400); }
.payment-status.failed    { color: var(--red-400); }

/* ── Notification Feed ───────────────────────────────────────────────── */
.notif-feed { display: flex; flex-direction: column; }
.notif-item { padding: 13px 20px; border-bottom: 1px solid var(--glass-border); transition: var(--transition); cursor: pointer; }
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: var(--glass-hover); }
.notif-item.unread { border-left: 3px solid var(--emerald-400); background: rgba(16,185,129,0.03); }
.notif-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--emerald-400); flex-shrink: 0; }

/* ── League Table ────────────────────────────────────────────────────── */
.standings-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.standings-table thead th { padding: 10px 14px; font-size: 10px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: var(--text-muted); background: var(--navy-750); text-align: left; }
.standings-table tbody td { padding: 10px 14px; border-bottom: 1px solid var(--glass-border); color: var(--text-primary); vertical-align: middle; }
.standings-table tbody tr:last-child td { border-bottom: none; }
.standings-table tbody tr:hover { background: var(--glass-hover); }
.standings-table tbody tr.top-row td { background: rgba(16,185,129,0.04); }
.s-pts { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 14px; color: var(--emerald-400); text-align: center; }

/* ── Chart Card ────────────────────────────────────────────────────── */
.chart-card { background: var(--navy-800); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 20px; }
.chart-card-header { padding: 14px 20px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: space-between; }
.chart-canvas-wrap { position: relative; height: 180px; padding: 16px 20px; }

/* ── Responsive ─────────────────────────────────────────────────────── */
@media (max-width: 1200px) {
    .stats-row-5 { grid-template-columns: repeat(3, 1fr); }
    .dash-cols { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .stats-row-5 { grid-template-columns: 1fr 1fr; }
    .match-details-grid { grid-template-columns: 1fr 1fr; }
    .dash-hero { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
    .stats-row-5 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- ── Hero Greeting ─────────────────────────────────────────────────── --}}
<div class="dash-hero">
    <div>
        <div class="hero-greeting">Welcome back</div>
        <div class="hero-name">{{ Str::words(auth()->user()->name, 2, '') }}</div>
        <div class="hero-sub">
            <span class="badge badge-{{ auth()->user()->role_color }}" style="font-size:10px;">{{ auth()->user()->role_label }}</span>
            @if(auth()->user()->position)
            <span style="margin-left:6px;font-size:13px;color:var(--text-muted);">· {{ auth()->user()->positionLabel() }}</span>
            @endif
        </div>
        <div class="hero-meta">
            @if($nextMatch)
            <span style="font-size:12px;color:var(--text-muted);">
                Next match: <span style="color:var(--emerald-400);font-weight:600;">{{ $nextMatch->formatted_date }}</span>
                at <span style="color:var(--text-primary);font-weight:600;">{{ $nextMatch->match_time }}</span>
            </span>
            @else
            <span style="font-size:12px;color:var(--text-muted);">No upcoming matches scheduled</span>
            @endif
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

{{-- ── 5 Stat Cards ──────────────────────────────────────────────────── --}}
<div class="stats-row-5">
    {{-- Personal Balance --}}
    <div class="stat-tile {{ $balance->isInCredit() ? 'emerald' : 'red' }}">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Personal Balance</div>
        <div class="stat-tile-value">KSh {{ number_format(abs($balance->balance)) }}</div>
        <div class="stat-tile-sub">{{ $balance->isInCredit() ? '✓ In good standing' : '⚠ Outstanding debt' }}</div>
        <div class="stat-tile-icon">💳</div>
    </div>
    {{-- Next Match --}}
    <div class="stat-tile blue">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Next Match</div>
        @if($nextMatch)
        <div class="stat-tile-value" style="font-size:16px;">{{ $nextMatch->formatted_date }}</div>
        <div class="stat-tile-sub">{{ $nextMatch->match_time }} · {{ $nextMatch->venue }}</div>
        @else
        <div class="stat-tile-value" style="font-size:18px;">—</div>
        <div class="stat-tile-sub">No match scheduled</div>
        @endif
        <div class="stat-tile-icon">⚽</div>
    </div>
    {{-- Availability Status --}}
    <div class="stat-tile {{ $myAvailability?->status === 'available' ? 'emerald' : ($myAvailability?->status === 'maybe' ? 'gold' : 'purple') }}">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Availability Status</div>
        @if($myAvailability)
        <div class="stat-tile-value" style="font-size:16px;">
            {{ $myAvailability->status === 'available' ? '🟢 Available' : ($myAvailability->status === 'maybe' ? '🟡 Maybe' : '🔴 Unavail') }}
        </div>
        <div class="stat-tile-sub">For next match</div>
        @elseif($nextMatch)
        <div class="stat-tile-value" style="font-size:16px;">❓ Not Set</div>
        <div class="stat-tile-sub">Respond below ↓</div>
        @else
        <div class="stat-tile-value" style="font-size:16px;">—</div>
        <div class="stat-tile-sub">No match upcoming</div>
        @endif
        <div class="stat-tile-icon">📋</div>
    </div>
    {{-- Payment History --}}
    <div class="stat-tile gold">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Total Paid</div>
        <div class="stat-tile-value">KSh {{ number_format($balance->total_paid) }}</div>
        <div class="stat-tile-sub">{{ $recentPayments->count() }} recent transactions</div>
        <div class="stat-tile-icon">💰</div>
    </div>
    {{-- Notifications --}}
    <div class="stat-tile cyan">
        <div class="stat-tile-accent"></div>
        <div class="stat-tile-label">Notifications</div>
        <div class="stat-tile-value">{{ $announcements->count() }}</div>
        <div class="stat-tile-sub">Club announcements</div>
        <div class="stat-tile-icon">🔔</div>
    </div>
</div>

{{-- ── Two-Column Body ─────────────────────────────────────────────── --}}
<div class="dash-cols">

    {{-- LEFT COLUMN --}}
    <div>
        {{-- Next Match Card --}}
        @if($nextMatch)
        <div class="match-hero-card">
            <div class="match-hero-banner">
                <div class="match-hero-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Next Match
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="badge {{ $nextMatch->status_badge['class'] }}">{{ $nextMatch->status_badge['label'] }}</span>
                    @if($nextMatch->type === 'league')
                    <span class="badge badge-blue">League</span>
                    @endif
                </div>
            </div>
            <div class="match-details-grid">
                <div class="match-detail-cell">
                    <div class="match-detail-label">Date</div>
                    <div class="match-detail-val">{{ $nextMatch->formatted_date }}</div>
                </div>
                <div class="match-detail-cell">
                    <div class="match-detail-label">Kick-off</div>
                    <div class="match-detail-val emerald">{{ $nextMatch->match_time }}</div>
                </div>
                <div class="match-detail-cell">
                    <div class="match-detail-label">Match Fee</div>
                    <div class="match-detail-val gold">KSh {{ number_format($nextMatch->match_fee) }}</div>
                </div>
            </div>
            <div style="padding:12px 22px;border-bottom:1px solid var(--glass-border);display:flex;align-items:center;gap:14px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span style="font-size:13px;color:var(--text-secondary);">{{ $nextMatch->venue }}</span>
                <div style="margin-left:auto;font-size:12px;color:{{ $nextMatch->isPastDeadline() ? 'var(--red-400)' : 'var(--gold-400)' }};">
                    @if($nextMatch->isPastDeadline()) 🔒 Deadline passed @else ⏰ Deadline: {{ $nextMatch->deadline->format('d M, H:i') }} @endif
                </div>
            </div>
            <div class="match-avail-zone">
                <div class="match-avail-label">Your Availability for this match</div>
                @if(!$nextMatch->isLocked())
                <form method="POST" action="{{ route('matches.availability', $nextMatch) }}" id="availForm">
                    @csrf
                    <input type="hidden" name="status" id="avail_status" value="{{ $myAvailability?->status ?? 'unavailable' }}">
                    <div class="avail-btns">
                        @php $cur = $myAvailability?->status ?? null; @endphp
                        <button type="submit" onclick="document.getElementById('avail_status').value='available'" class="avail-btn {{ $cur==='available' ? 'active-avail' : '' }}">🟢 Available</button>
                        <button type="submit" onclick="document.getElementById('avail_status').value='maybe'"     class="avail-btn {{ $cur==='maybe' ? 'active-maybe' : '' }}">🟡 Maybe</button>
                        <button type="submit" onclick="document.getElementById('avail_status').value='unavailable'" class="avail-btn {{ $cur==='unavailable' ? 'active-unavail' : '' }}">🔴 Can't make it</button>
                    </div>
                </form>
                @if($myAvailability)
                <div class="current-status-row">
                    <span>Your current status:</span>
                    <span class="badge {{ $myAvailability->getStatusBadge()['class'] }}">
                        {{ $myAvailability->getStatusBadge()['icon'] }} {{ $myAvailability->getStatusBadge()['label'] }}
                    </span>
                </div>
                @endif
                @else
                <div class="alert alert-warning" style="margin:0;">🔒 Availability is locked. Contact your admin for any changes.</div>
                @endif
            </div>
        </div>
        @endif

        {{-- Payment History Chart --}}
        <div class="chart-card">
            <div class="chart-card-header">
                <span class="card-title" style="font-size:14px;">💰 Payment History</span>
                <span class="badge badge-green" style="font-size:10px;">Last 6 Months</span>
            </div>
            <div class="chart-canvas-wrap">
                <canvas id="chartMemberPay"></canvas>
            </div>
        </div>

        {{-- Upcoming Matches List --}}
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <span class="card-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;margin-right:6px;vertical-align:-2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Upcoming Matches
                </span>
                <a href="{{ route('matches.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            @if($upcomingMatches->count())
            <div class="match-list">
                @foreach($upcomingMatches as $m)
                <a href="{{ route('matches.show', $m) }}" class="match-item">
                    <div class="match-date-box">
                        <div class="match-date-day">{{ $m->match_date->format('d') }}</div>
                        <div class="match-date-mon">{{ $m->match_date->format('M') }}</div>
                    </div>
                    <div class="match-item-info">
                        <div class="match-item-venue">{{ $m->venue }}</div>
                        <div class="match-item-time">{{ $m->match_time }} · {{ ucfirst($m->type) }}</div>
                    </div>
                    <div class="match-item-fee">KSh {{ number_format($m->match_fee) }}</div>
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--text-muted);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding:40px 20px;">
                <div class="empty-state-icon">📅</div>
                <p>No upcoming matches scheduled</p>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div>
        {{-- Personal Balance Card --}}
        <div class="balance-card">
            <div class="balance-card-header">
                <span class="card-title" style="font-size:14px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;margin-right:6px;vertical-align:-2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    My Balance
                </span>
                <span class="badge {{ $balance->getStatusClass() }}" style="font-size:10px;">{{ $balance->getStatusLabel() }}</span>
            </div>
            <div class="balance-card-body">
                <div class="balance-main">
                    {{-- SVG Ring --}}
                    @php
                        $pct  = $balance->total_owed > 0 ? min(100, ($balance->total_paid / max(1,$balance->total_owed)) * 100) : 100;
                        $r    = 38; $circ = 2 * M_PI * $r;
                        $dash = ($pct / 100) * $circ;
                    @endphp
                    <div class="balance-ring-wrap">
                        <svg width="88" height="88" viewBox="0 0 88 88">
                            <circle cx="44" cy="44" r="{{ $r }}" fill="none" stroke="var(--navy-750)" stroke-width="8"/>
                            <circle cx="44" cy="44" r="{{ $r }}" fill="none"
                                stroke="{{ $balance->isInCredit() ? 'var(--emerald-400)' : 'var(--red-400)' }}"
                                stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ number_format($dash, 2) }} {{ number_format($circ, 2) }}"
                                style="transition: stroke-dasharray 1s ease;"/>
                        </svg>
                        <div class="balance-ring-center">
                            <div style="font-size:22px;line-height:1;">{{ $balance->isInCredit() ? '✅' : '⚠️' }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="balance-amount {{ $balance->isInCredit() ? 'credit' : 'overdue' }}">
                            KSh {{ number_format(abs($balance->balance)) }}
                        </div>
                        <div class="balance-desc">{{ $balance->isInCredit() ? 'Advance / Credit' : 'Outstanding owed' }}</div>
                    </div>
                </div>
                <div class="balance-rows">
                    <div class="balance-row">
                        <span class="balance-row-label">Total Contributed</span>
                        <span style="font-weight:600;color:var(--emerald-400);">KSh {{ number_format($balance->total_paid) }}</span>
                    </div>
                    @if($balance->last_payment_at)
                    <div class="balance-row">
                        <span class="balance-row-label">Last Payment</span>
                        <span style="font-weight:600;">{{ $balance->last_payment_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    <div class="balance-row">
                        <span class="balance-row-label">Matches Attended</span>
                        <span style="font-weight:600;color:var(--blue-400);">{{ $matchesPlayed }} / {{ $totalCompleted }}</span>
                    </div>
                    <div class="balance-row">
                        <span class="balance-row-label">Attendance Rate</span>
                        <span style="font-weight:700;color:{{ $memberAttendanceRate >= 70 ? 'var(--emerald-400)' : ($memberAttendanceRate >= 40 ? 'var(--gold-400)' : 'var(--red-400)') }};">
                            {{ $memberAttendanceRate }}%
                        </span>
                    </div>
                    <div style="margin-top:6px;">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm w-full" style="justify-content:center;">View Full Payment History</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications / Announcements --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title" style="font-size:14px;">
                    🔔 Notifications
                </span>
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary btn-sm">All</a>
            </div>
            <div class="notif-feed">
                @forelse($announcements as $ann)
                <div class="notif-item" data-id="{{ $ann->id }}">
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <div style="flex-shrink:0;margin-top:2px;">
                            @if($ann->type === 'urgent') 🚨
                            @elseif($ann->type === 'match_reminder') ⚽
                            @elseif($ann->type === 'payment') 💰
                            @else 📢
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="font-bold text-sm" style="color:var(--text-primary);">{{ $ann->title }}</div>
                            <div class="text-xs text-secondary" style="margin-top:3px;line-height:1.4;">{{ Str::limit($ann->body, 80) }}</div>
                            <div class="text-xs text-muted" style="margin-top:4px;">{{ $ann->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="badge {{ $ann->getTypeBadgeClass() }}" style="font-size:8px;flex-shrink:0;">{{ $ann->getTypeLabel() }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:30px;">
                    <div style="font-size:28px;margin-bottom:8px;opacity:0.4;">🔔</div>
                    <p class="text-xs text-muted">No notifications at this time</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- League Standings --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="font-size:14px;">🏆 League Standings</span>
                <a href="{{ route('league.standings') }}" class="btn btn-secondary btn-sm">Full Table</a>
            </div>
            @if($standings->count())
            <table class="standings-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Team</th>
                        <th style="text-align:center;">W</th>
                        <th style="text-align:center;">D</th>
                        <th style="text-align:center;">L</th>
                        <th style="text-align:center;">Pts</th>
                        <th style="text-align:center;">GD</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($standings as $i => $s)
                <tr class="{{ $i === 0 ? 'top-row' : '' }}">
                    <td style="font-family:'Outfit',sans-serif;font-weight:800;font-size:15px;width:36px;text-align:center;color:{{ $i === 0 ? 'var(--gold-400)' : 'var(--text-muted)' }};">{{ $i + 1 }}</td>
                    <td>
                        <span style="font-weight:700;font-size:13px;">{{ $s->team?->short_name ?? 'Team '.($i+1) }}</span>
                        @if($i === 0)<span class="badge badge-yellow" style="font-size:8px;margin-left:6px;">Leader</span>@endif
                    </td>
                    <td style="text-align:center;color:var(--text-secondary);font-size:12px;">{{ $s->wins ?? '—' }}</td>
                    <td style="text-align:center;color:var(--text-secondary);font-size:12px;">{{ $s->draws ?? '—' }}</td>
                    <td style="text-align:center;color:var(--text-secondary);font-size:12px;">{{ $s->losses ?? '—' }}</td>
                    <td class="s-pts">{{ $s->points }}</td>
                    <td style="text-align:center;font-size:12px;color:{{ $s->goal_difference >= 0 ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                        {{ $s->goal_difference > 0 ? '+' : '' }}{{ $s->goal_difference }}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state" style="padding:30px;">
                <div style="font-size:28px;margin-bottom:8px;opacity:0.4;">🏆</div>
                <p class="text-xs text-muted">No standings for this season yet</p>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const months  = {!! $memberChartMonths !!};
    const payData = {!! $memberChartPay !!};

    Chart.defaults.color = 'rgba(148,163,184,0.85)';
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size   = 11;

    new Chart(document.getElementById('chartMemberPay'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Contributions (KSh)',
                data: payData,
                backgroundColor: 'rgba(16,185,129,0.22)',
                borderColor: 'rgba(16,185,129,0.9)',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(16,185,129,0.42)',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' KSh ' + ctx.parsed.y.toLocaleString() } }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false }, ticks: { padding: 6 } },
                y: { grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false }, beginAtZero: true,
                     ticks: { padding: 6, callback: v => 'KSh ' + (v/1000).toFixed(0) + 'k' } }
            }
        }
    });
})();
</script>
@endpush
