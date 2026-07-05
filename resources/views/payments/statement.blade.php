@extends('layouts.app')
@section('title', 'Member Statement — ' . $user->name)
@section('page-title', 'Member Statement')

@push('styles')
<style>
.stmt-wrap { max-width: 780px; margin: 0 auto; }
.stmt-header {
    background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-750) 100%);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl); padding: 28px 32px; margin-bottom: 24px;
    display:flex; align-items:center; justify-content:space-between; gap:20px;
}
.stmt-org { font-size:10px; color:var(--text-muted); letter-spacing:1px; text-transform:uppercase; }
.stmt-logo { font-family:'Outfit',sans-serif; font-weight:900; font-size:22px; color:var(--emerald-400); }
.stmt-title { font-size:14px; color:var(--text-secondary); margin-top:2px; }
.stmt-meta { font-size:11px; color:var(--text-muted); margin-top:6px; }
.stmt-balance-block { text-align:right; }
.stmt-bal-label { font-size:10px; color:var(--text-muted); letter-spacing:1px; text-transform:uppercase; }
.stmt-bal-value { font-family:'Outfit',sans-serif; font-weight:900; font-size:32px; line-height:1; }
.stmt-bal-value.credit { color: var(--emerald-400); }
.stmt-bal-value.debt   { color: var(--red-400); }
.stmt-summary-row {
    display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;
}
.stmt-sum-tile {
    background:var(--navy-800); border:1px solid var(--glass-border);
    border-radius:var(--radius-lg); padding:16px 20px; text-align:center;
}
.stmt-sum-tile .label { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; }
.stmt-sum-tile .value { font-family:'Outfit',sans-serif; font-size:20px; font-weight:900; }
.ledger-table { width:100%; border-collapse:collapse; font-size:13px; }
.ledger-table thead th { padding:11px 16px; font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--text-muted); background:var(--navy-750); text-align:left; }
.ledger-table thead th:last-child, .ledger-table tbody td:last-child { text-align:right; }
.ledger-table tbody td { padding:12px 16px; border-bottom:1px solid var(--glass-border); vertical-align:middle; }
.ledger-table tbody tr:last-child td { border-bottom:none; }
.ledger-table tbody tr:hover { background:var(--glass-hover); }
.ledger-table .debit  { color:var(--red-400);   font-weight:700; }
.ledger-table .credit { color:var(--emerald-400); font-weight:700; }
.ledger-table .bal-pos { color:var(--emerald-400); font-weight:800; font-family:'Outfit',sans-serif; }
.ledger-table .bal-neg { color:var(--red-400);    font-weight:800; font-family:'Outfit',sans-serif; }
@media print {
    .sidebar, .topbar, .btn, .print-actions, .breadcrumb, .app-header { display:none !important; }
    body, html, .main-content { background:#fff !important; color:#000 !important; padding:0 !important; margin:0 !important; }
    .stmt-header { background:#f0fdf4 !important; border-color:#d1fae5 !important; color:#000 !important; }
    .stmt-logo { color:#16a34a !important; }
    .stmt-bal-value.credit { color:#16a34a !important; }
    .stmt-bal-value.debt   { color:#dc2626 !important; }
    .card, .stmt-sum-tile { background:#fff !important; border-color:#e5e7eb !important; }
    .ledger-table thead th { background:#f9fafb !important; color:#6b7280 !important; }
    .ledger-table .debit  { color:#dc2626 !important; }
    .ledger-table .credit { color:#16a34a !important; }
    .ledger-table .bal-pos { color:#16a34a !important; }
    .ledger-table .bal-neg { color:#dc2626 !important; }
}
</style>
@endpush

@section('content')
<div class="stmt-wrap">
    {{-- Print Actions --}}
    <div class="print-actions" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">← Back to Payments</a>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print Statement</button>
            @if(auth()->user()->isAdmin() || auth()->user()->isTreasurer())
            <a href="{{ route('payments.statement.user', $user) }}" class="btn btn-secondary btn-sm">📋 This Member</a>
            @endif
        </div>
    </div>

    {{-- Statement Header --}}
    <div class="stmt-header">
        <div>
            <div class="stmt-org">Beijing FC Management System</div>
            <div class="stmt-logo">BFC — Official Member Statement</div>
            <div class="stmt-title">{{ $user->name }}</div>
            <div class="stmt-meta">
                <span class="badge badge-{{ $user->role_color }}">{{ $user->role_label }}</span>
                <span style="margin-left:8px;">
                    @if($user->billing_type === 'monthly')
                    📅 Monthly Subscription (KSh 2,080/mo)
                    @else
                    ⚽ Pay Per Match (KSh 350/match)
                    @endif
                </span>
            </div>
            <div class="stmt-meta" style="margin-top:6px;">
                Joined: {{ $user->date_joined?->format('d M Y') ?? 'N/A' }} &nbsp;|&nbsp;
                Statement Date: {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="stmt-balance-block">
            @if($user->balance)
            <div class="stmt-bal-label">Net Balance</div>
            <div class="stmt-bal-value {{ $user->balance->isInCredit() ? 'credit' : 'debt' }}">
                {{ $user->balance->isInCredit() ? '+' : '-' }}KSh {{ number_format(abs($user->balance->balance)) }}
            </div>
            <div style="margin-top:8px;">
                <span class="badge {{ $user->balance->getStatusClass() }}">{{ $user->balance->getStatusLabel() }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Summary Tiles --}}
    @if($user->balance)
    <div class="stmt-summary-row">
        <div class="stmt-sum-tile">
            <div class="label">Total Contributed</div>
            <div class="value" style="color:var(--emerald-400);">KSh {{ number_format($user->balance->total_paid) }}</div>
        </div>
        <div class="stmt-sum-tile">
            <div class="label">Total Charged</div>
            <div class="value" style="color:var(--red-400);">KSh {{ number_format($user->balance->total_owed) }}</div>
        </div>
        <div class="stmt-sum-tile">
            <div class="label">Outstanding / Credit</div>
            <div class="value" style="color:{{ $user->balance->isInCredit() ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                {{ $user->balance->isInCredit() ? '+' : '-' }}KSh {{ number_format(abs($user->balance->balance)) }}
            </div>
        </div>
    </div>
    @endif

    {{-- Ledger Table --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📒 Account Ledger</span>
            <span style="font-size:11px;color:var(--text-muted);">Debits are charges · Credits are payments</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th style="text-align:right;">Debit (Dr)</th>
                        <th style="text-align:right;">Credit (Cr)</th>
                        <th style="text-align:right;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ledger as $row)
                <tr>
                    <td class="text-xs text-muted" style="white-space:nowrap;">
                        {{ $row['date'] instanceof \Illuminate\Support\Carbon ? $row['date']->format('d M Y') : \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                    </td>
                    <td class="text-xs" style="font-family:monospace;color:var(--text-muted);">{{ $row['ref'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td class="debit">{{ $row['debit'] > 0 ? 'KSh ' . number_format($row['debit']) : '—' }}</td>
                    <td class="credit">{{ $row['credit'] > 0 ? 'KSh ' . number_format($row['credit']) : '—' }}</td>
                    <td class="{{ $row['running_balance'] >= 0 ? 'bal-pos' : 'bal-neg' }}">
                        {{ $row['running_balance'] >= 0 ? '+' : '-' }}KSh {{ number_format(abs($row['running_balance'])) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">No ledger entries found.</td>
                </tr>
                @endforelse
                </tbody>
                @if($ledger->count())
                <tfoot>
                    <tr style="background:var(--navy-750);font-weight:800;">
                        <td colspan="3" style="padding:12px 16px;font-size:12px;text-transform:uppercase;letter-spacing:.8px;">Closing Balance</td>
                        <td style="padding:12px 16px;text-align:right;color:var(--red-400);">KSh {{ number_format($ledger->sum('debit')) }}</td>
                        <td style="padding:12px 16px;text-align:right;color:var(--emerald-400);">KSh {{ number_format($ledger->sum('credit')) }}</td>
                        @php $final = $ledger->last()['running_balance'] ?? 0; @endphp
                        <td style="padding:12px 16px;text-align:right;color:{{ $final >= 0 ? 'var(--emerald-400)' : 'var(--red-400)' }};font-family:'Outfit',sans-serif;font-size:15px;">
                            {{ $final >= 0 ? '+' : '-' }}KSh {{ number_format(abs($final)) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Footer note --}}
    <div style="text-align:center;font-size:11px;color:var(--text-muted);margin-top:16px;padding:12px;">
        This is an official financial statement generated by the Beijing FC Management System.<br>
        For queries, contact the Club Treasurer. Generated: {{ now()->format('d M Y, H:i') }}
    </div>
</div>
@endsection
