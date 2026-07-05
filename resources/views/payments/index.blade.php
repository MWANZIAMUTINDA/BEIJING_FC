@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Financial Payments')

@push('styles')
<style>
.fin-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:28px; }
.fin-tile {
    background:var(--navy-800); border:1px solid var(--glass-border);
    border-radius:var(--radius-lg); padding:22px 24px; position:relative;
    overflow:hidden; transition:var(--transition);
}
.fin-tile:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(0,0,0,.4); }
.fin-tile-accent { position:absolute; top:0; left:0; width:4px; height:100%; border-radius:4px 0 0 4px; }
.fin-tile.emerald .fin-tile-accent { background:linear-gradient(180deg,var(--emerald-400),var(--emerald-600)); }
.fin-tile.gold    .fin-tile-accent { background:linear-gradient(180deg,var(--gold-300),var(--gold-500)); }
.fin-tile.blue    .fin-tile-accent { background:linear-gradient(180deg,var(--blue-400),var(--blue-500)); }
.fin-tile-label { font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--text-muted); margin-bottom:8px; }
.fin-tile-value { font-family:'Outfit',sans-serif; font-size:26px; font-weight:900; line-height:1; }
.fin-tile.emerald .fin-tile-value { color:var(--emerald-400); }
.fin-tile.gold    .fin-tile-value { color:var(--gold-400); }
.fin-tile.blue    .fin-tile-value { color:var(--blue-400); }
.fin-tile-sub  { font-size:11px; color:var(--text-muted); margin-top:6px; }
.fin-tile-icon { position:absolute; right:18px; top:50%; transform:translateY(-50%); font-size:32px; opacity:.10; }
.table-wrap { overflow-x:auto; }
.table-premium { width:100%; border-collapse:collapse; font-size:13px; }
.table-premium thead th { padding:12px 16px; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); background:var(--navy-750); text-align:left; }
.table-premium tbody td { padding:13px 16px; border-bottom:1px solid var(--glass-border); color:var(--text-primary); vertical-align:middle; }
.table-premium tbody tr:last-child td { border-bottom:none; }
.table-premium tbody tr:hover { background:var(--glass-hover); }
@media(max-width:768px){.fin-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.fin-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')

{{-- Summary Tiles --}}
<div class="fin-grid">
    <div class="fin-tile emerald">
        <div class="fin-tile-accent"></div>
        <div class="fin-tile-label">Total Confirmed</div>
        <div class="fin-tile-value">KSh {{ number_format($summary['total_confirmed']) }}</div>
        <div class="fin-tile-sub">All confirmed payments</div>
        <div class="fin-tile-icon">💰</div>
    </div>
    <div class="fin-tile gold">
        <div class="fin-tile-accent"></div>
        <div class="fin-tile-label">Pending Clearance</div>
        <div class="fin-tile-value">KSh {{ number_format($summary['total_pending']) }}</div>
        <div class="fin-tile-sub">Awaiting confirmation</div>
        <div class="fin-tile-icon">⏳</div>
    </div>
    <div class="fin-tile blue">
        <div class="fin-tile-accent"></div>
        <div class="fin-tile-label">Today's Payments</div>
        <div class="fin-tile-value">{{ $summary['count_today'] }}</div>
        <div class="fin-tile-sub">Transactions today</div>
        <div class="fin-tile-icon">📅</div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <span class="card-title">🔍 Filter Payments</span>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if(auth()->user()->isAdmin() || auth()->user()->isTreasurer())
            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">+ Record Payment</a>
            <a href="{{ route('payments.export') }}" class="btn btn-secondary btn-sm">⬇ CSV</a>
            <a href="{{ route('payments.treasurer_report') }}" class="btn btn-secondary btn-sm">📊 Treasurer Report</a>
            @endif
            <a href="{{ route('payments.statement') }}" class="btn btn-secondary btn-sm">📋 My Statement</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('payments.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr)) 100px;gap:16px;align-items:end;">
            @if(auth()->user()->isAdmin() || auth()->user()->isTreasurer())
            <div>
                <label class="form-label">Member</label>
                <select name="user_id" class="form-control">
                    <option value="">All Members</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ request('user_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="form-label">Payment Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Monthly (KSh 2,080)</option>
                    <option value="match"   {{ request('type') === 'match'   ? 'selected' : '' }}>Match Fee (KSh 350)</option>
                    <option value="partial" {{ request('type') === 'partial' ? 'selected' : '' }}>Partial Payment</option>
                    <option value="penalty" {{ request('type') === 'penalty' ? 'selected' : '' }}>Penalty</option>
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Search</button>
            </div>
        </form>
    </div>
</div>

{{-- Payments Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">💳 Payment Transactions</span>
    </div>
    <div class="table-wrap">
        @if($payments->count())
        <table class="table-premium">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>M-Pesa Code</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($payments as $p)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img src="{{ $p->user?->avatar_url }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:1.5px solid var(--glass-border);">
                        <span style="font-weight:600;">{{ $p->user?->name ?? 'Unknown' }}</span>
                    </div>
                </td>
                <td><strong class="text-emerald">KSh {{ number_format($p->amount) }}</strong></td>
                <td>
                    <span class="badge badge-{{ $p->type === 'monthly' ? 'blue' : ($p->type === 'match' ? 'emerald' : ($p->type === 'partial' ? 'yellow' : 'red')) }}">
                        {{ $p->getTypeLabel() }}
                    </span>
                </td>
                <td class="text-xs" style="font-family:monospace;letter-spacing:.5px;">{{ $p->mpesa_code ?? '—' }}</td>
                <td><span class="badge {{ $p->getStatusBadge()['class'] }}">{{ $p->getStatusBadge()['label'] }}</span></td>
                <td class="text-xs text-muted">{{ $p->created_at->format('d M Y, H:i') }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('payments.receipt', $p) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">🧾 Receipt</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isTreasurer())
                    @if($p->status === 'pending')
                    <form method="POST" action="{{ route('payments.reconcile') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="payment_id" value="{{ $p->id }}">
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding:4px 8px;">✓ Confirm</button>
                    </form>
                    @endif
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <div class="empty-state-title">No payments found</div>
            <p class="text-xs text-muted">Try adjusting your filters.</p>
        </div>
        @endif
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
