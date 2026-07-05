@extends('layouts.app')
@section('title', 'Treasurer Report')
@section('page-title', 'Treasurer Financial Report')

@push('styles')
<style>
.tr-kpi { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:28px; }
.tr-tile {
    background:var(--navy-800); border:1px solid var(--glass-border);
    border-radius:var(--radius-lg); padding:22px 24px; position:relative; overflow:hidden;
    transition:var(--transition);
}
.tr-tile:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.4); }
.tr-tile-accent { position:absolute; top:0; left:0; width:4px; height:100%; border-radius:4px 0 0 4px; }
.tr-tile.emerald .tr-tile-accent { background:linear-gradient(180deg,var(--emerald-400),var(--emerald-600)); }
.tr-tile.gold    .tr-tile-accent { background:linear-gradient(180deg,var(--gold-300),var(--gold-500)); }
.tr-tile.red     .tr-tile-accent { background:linear-gradient(180deg,var(--red-400),var(--red-500)); }
.tr-tile.blue    .tr-tile-accent { background:linear-gradient(180deg,var(--blue-400),var(--blue-500)); }
.tr-tile-label { font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--text-muted); margin-bottom:8px; }
.tr-tile-value { font-family:'Outfit',sans-serif; font-size:24px; font-weight:900; line-height:1; }
.tr-tile.emerald .tr-tile-value { color:var(--emerald-400); }
.tr-tile.gold    .tr-tile-value { color:var(--gold-400); }
.tr-tile.red     .tr-tile-value { color:var(--red-400); }
.tr-tile.blue    .tr-tile-value { color:var(--blue-400); }
.tr-tile-sub { font-size:11px; color:var(--text-muted); margin-top:6px; }
.tr-tile-icon { position:absolute; right:18px; top:50%; transform:translateY(-50%); font-size:32px; opacity:.10; }

.tr-cols { display:grid; grid-template-columns:1fr 340px; gap:24px; margin-bottom:28px; }
.type-breakdown-row { display:flex; flex-direction:column; gap:0; }
.type-row { padding:13px 20px; border-bottom:1px solid var(--glass-border); display:flex; align-items:center; justify-content:space-between; }
.type-row:last-child { border-bottom:none; }
.type-bar-wrap { flex:1; background:var(--navy-750); border-radius:999px; height:6px; margin:0 16px; overflow:hidden; }
.type-bar { height:100%; border-radius:999px; }

.status-donut-wrap { display:flex; justify-content:center; padding:20px; }
.status-legend { padding:0 20px 16px; }
.status-leg-row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; font-size:13px; border-bottom:1px solid var(--glass-border); }
.status-leg-row:last-child { border-bottom:none; }
.status-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

.roster-table { width:100%; border-collapse:collapse; font-size:12px; }
.roster-table thead th { padding:10px 16px; font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); background:var(--navy-750); text-align:left; }
.roster-table tbody td { padding:11px 16px; border-bottom:1px solid var(--glass-border); vertical-align:middle; }
.roster-table tbody tr:last-child td { border-bottom:none; }
.roster-table tbody tr:hover { background:var(--glass-hover); }

.efficiency-bar { height:12px; background:var(--navy-750); border-radius:999px; overflow:hidden; margin:10px 0; }
.efficiency-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,var(--emerald-500),var(--emerald-400)); transition:width 1s ease; }

@media print {
    .sidebar, .topbar, .btn, .print-actions, .breadcrumb { display:none !important; }
    body, html, .main-content { background:#fff !important; color:#000 !important; }
    .card, .tr-tile { background:#fff !important; border-color:#e5e7eb !important; }
    .tr-tile-value { color:#000 !important; }
    .roster-table thead th { background:#f9fafb !important; }
}
@media(max-width:1200px){ .tr-kpi{grid-template-columns:repeat(2,1fr);} .tr-cols{grid-template-columns:1fr;} }
@media(max-width:600px){ .tr-kpi{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

{{-- Print Actions --}}
<div class="print-actions" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <span class="text-xs text-muted">Generated: {{ now()->format('d M Y, H:i') }}</span>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()" class="btn btn-secondary btn-sm">🖨️ Print Report</button>
        <a href="{{ route('payments.export') }}" class="btn btn-secondary btn-sm">⬇ CSV Export</a>
        <a href="{{ route('payments.index') }}" class="btn btn-primary btn-sm">← Payments</a>
    </div>
</div>

{{-- KPI Tiles --}}
<div class="tr-kpi">
    <div class="tr-tile emerald">
        <div class="tr-tile-accent"></div>
        <div class="tr-tile-label">Total Income</div>
        <div class="tr-tile-value">KSh {{ number_format($totalIncome) }}</div>
        <div class="tr-tile-sub">All confirmed payments</div>
        <div class="tr-tile-icon">💰</div>
    </div>
    <div class="tr-tile red">
        <div class="tr-tile-accent"></div>
        <div class="tr-tile-label">Total Expenses</div>
        <div class="tr-tile-value">KSh {{ number_format($totalExpenses) }}</div>
        <div class="tr-tile-sub">Approved expenses</div>
        <div class="tr-tile-icon">💸</div>
    </div>
    <div class="tr-tile {{ $netFunds >= 0 ? 'emerald' : 'red' }}">
        <div class="tr-tile-accent"></div>
        <div class="tr-tile-label">Net Fund Balance</div>
        <div class="tr-tile-value">KSh {{ number_format(abs($netFunds)) }}</div>
        <div class="tr-tile-sub">{{ $netFunds >= 0 ? '✅ Club in surplus' : '⚠️ Club in deficit' }}</div>
        <div class="tr-tile-icon">📊</div>
    </div>
    <div class="tr-tile blue">
        <div class="tr-tile-accent"></div>
        <div class="tr-tile-label">Collection Rate</div>
        <div class="tr-tile-value">{{ $efficiency }}%</div>
        <div class="tr-tile-sub">KSh {{ number_format($totalOutstandingDebt) }} outstanding</div>
        <div class="tr-tile-icon">📈</div>
    </div>
</div>

{{-- Collection Progress --}}
<div class="card" style="margin-bottom:28px;">
    <div class="card-header">
        <span class="card-title">📈 Collection Efficiency</span>
        <span class="badge {{ $efficiency >= 80 ? 'badge-green' : ($efficiency >= 50 ? 'badge-yellow' : 'badge-red') }}">{{ $efficiency }}%</span>
    </div>
    <div class="card-body">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:6px;">
            <span>KSh 0</span>
            <span>{{ $efficiency }}% Collected</span>
            <span>KSh {{ number_format($totalIncome + $totalOutstandingDebt) }} Target</span>
        </div>
        <div class="efficiency-bar">
            <div class="efficiency-fill" style="width:{{ $efficiency }}%;"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:16px;text-align:center;">
            <div>
                <div style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:var(--emerald-400);">KSh {{ number_format($totalIncome) }}</div>
                <div class="text-xs text-muted">Collected</div>
            </div>
            <div>
                <div style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:var(--red-400);">KSh {{ number_format($totalOutstandingDebt) }}</div>
                <div class="text-xs text-muted">Outstanding Debt</div>
            </div>
            <div>
                <div style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:var(--gold-400);">KSh {{ number_format($totalIncome + $totalOutstandingDebt) }}</div>
                <div class="text-xs text-muted">Total Expected</div>
            </div>
        </div>
    </div>
</div>

{{-- Two Column: Type Breakdown + Status Counts --}}
<div class="tr-cols">
    {{-- Payment Type Breakdown --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💳 Income by Payment Type</span>
        </div>
        <div class="type-breakdown-row">
            @php
                $types = [
                    'monthly' => ['label' => 'Monthly (KSh 2,080)', 'color' => '#3B82F6'],
                    'match'   => ['label' => 'Match Fee (KSh 350)', 'color' => '#10B981'],
                    'partial' => ['label' => 'Partial Payments',    'color' => '#F59E0B'],
                    'penalty' => ['label' => 'Penalties',           'color' => '#EF4444'],
                ];
                $maxAmount = $typeBreakdown->max('total') ?: 1;
            @endphp
            @foreach($types as $key => $meta)
            @php $row = $typeBreakdown[$key] ?? null; @endphp
            <div class="type-row">
                <div style="min-width:150px;">
                    <div class="text-xs font-bold" style="color:var(--text-primary);">{{ $meta['label'] }}</div>
                    <div class="text-xs text-muted">{{ $row ? $row->count . ' transactions' : '0 transactions' }}</div>
                </div>
                <div class="type-bar-wrap">
                    <div class="type-bar" style="width:{{ $row ? ($row->total / $maxAmount * 100) : 0 }}%;background:{{ $meta['color'] }};"></div>
                </div>
                <div style="min-width:90px;text-align:right;font-family:'Outfit',sans-serif;font-weight:800;font-size:13px;color:{{ $meta['color'] }};">
                    KSh {{ $row ? number_format($row->total) : '0' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Member Status Distribution --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Member Status</span>
        </div>
        <div class="status-legend">
            @php
                $statusColors = [
                    'Paid'        => '#10B981',
                    'Pending'     => '#F59E0B',
                    'Partial'     => '#FB923C',
                    'Outstanding' => '#EF4444',
                ];
                $total = array_sum($statusCounts);
            @endphp
            @foreach($statusColors as $status => $color)
            <div class="status-leg-row">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div class="status-dot" style="background:{{ $color }};"></div>
                    <span class="font-bold text-sm">{{ $status }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-family:'Outfit',sans-serif;font-weight:800;font-size:16px;color:{{ $color }};">{{ $statusCounts[$status] }}</span>
                    <span class="text-xs text-muted">{{ $total > 0 ? round($statusCounts[$status] / $total * 100) : 0 }}%</span>
                </div>
            </div>
            @endforeach
            <div style="padding-top:12px;text-align:center;font-size:12px;color:var(--text-muted);">
                Total of <strong>{{ $total }}</strong> member records
            </div>
        </div>
    </div>
</div>

{{-- Full Member Roster with Balances --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Full Member Financial Roster</span>
        <a href="{{ route('payments.export') }}" class="btn btn-secondary btn-sm">⬇ Export CSV</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="roster-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Billing Type</th>
                    <th style="text-align:right;">Total Paid</th>
                    <th style="text-align:right;">Total Owed</th>
                    <th style="text-align:right;">Balance</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Last Payment</th>
                </tr>
            </thead>
            <tbody>
            @forelse($balances as $bal)
            @if($bal->user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <img src="{{ $bal->user->avatar_url }}" style="width:26px;height:26px;border-radius:50%;object-fit:cover;border:1px solid var(--glass-border);">
                        <div>
                            <div class="font-bold" style="font-size:12px;">{{ $bal->user->name }}</div>
                            <div class="text-xs text-muted">{{ $bal->user->positionLabel() }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $bal->user->billing_type === 'monthly' ? 'badge-blue' : 'badge-emerald' }}" style="font-size:9px;">
                        {{ $bal->user->billing_type === 'monthly' ? '📅 Monthly' : '⚽ Per Match' }}
                    </span>
                </td>
                <td style="text-align:right;font-weight:700;color:var(--emerald-400);">KSh {{ number_format($bal->total_paid) }}</td>
                <td style="text-align:right;font-weight:700;color:var(--red-400);">KSh {{ number_format($bal->total_owed) }}</td>
                <td style="text-align:right;font-weight:800;font-family:'Outfit',sans-serif;font-size:13px;color:{{ $bal->isInCredit() ? 'var(--emerald-400)' : 'var(--red-400)' }};">
                    {{ $bal->isInCredit() ? '+' : '-' }}KSh {{ number_format(abs($bal->balance)) }}
                </td>
                <td style="text-align:center;">
                    <span class="badge {{ $bal->getStatusClass() }}" style="font-size:9px;">{{ $bal->getStatusLabel() }}</span>
                </td>
                <td style="text-align:center;font-size:11px;color:var(--text-muted);">
                    {{ $bal->last_payment_at ? $bal->last_payment_at->format('d M Y') : '—' }}
                </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="7" style="text-align:center;padding:28px;color:var(--text-muted);">No member balance records found.</td></tr>
            @endforelse
            </tbody>
            @if($balances->count())
            <tfoot>
                <tr style="background:var(--navy-750);font-weight:800;">
                    <td colspan="2" style="padding:12px 16px;font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Club Totals</td>
                    <td style="padding:12px 16px;text-align:right;color:var(--emerald-400);">KSh {{ number_format($balances->sum('total_paid')) }}</td>
                    <td style="padding:12px 16px;text-align:right;color:var(--red-400);">KSh {{ number_format($balances->sum('total_owed')) }}</td>
                    @php $netBal = $balances->sum('balance'); @endphp
                    <td style="padding:12px 16px;text-align:right;color:{{ $netBal >= 0 ? 'var(--emerald-400)' : 'var(--red-400)' }};font-family:'Outfit',sans-serif;">
                        {{ $netBal >= 0 ? '+' : '-' }}KSh {{ number_format(abs($netBal)) }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<div style="text-align:center;font-size:11px;color:var(--text-muted);margin-top:16px;padding-bottom:16px;">
    Confidential — Treasurer Report · Beijing FC Management System · {{ now()->format('d M Y') }}
</div>
@endsection

@push('scripts')
<script>
// Animate efficiency bar on load
document.addEventListener('DOMContentLoaded', () => {
    const fill = document.querySelector('.efficiency-fill');
    if (fill) {
        const target = fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => fill.style.width = target, 100);
    }
});
</script>
@endpush
