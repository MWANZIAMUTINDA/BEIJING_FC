@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Club Expenses')

@push('styles')
<style>
/* Modern styling for the monthly expense trend dashboard chart */
.expense-chart-container {
    background: var(--surface-2);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}
.chart-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 16px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}
.visual-bar-chart {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 180px;
    padding-top: 20px;
    border-bottom: 1px solid var(--glass-border);
    gap: 12px;
}
.bar-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    justify-content: flex-end;
}
.bar-pill {
    width: 100%;
    max-width: 48px;
    background: linear-gradient(180deg, #f59e0b, #d97706);
    border-radius: 6px 6px 0 0;
    min-height: 4px;
    transition: height 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.bar-pill:hover {
    background: linear-gradient(180deg, #fbbf24, #f59e0b);
}
.bar-tooltip {
    position: absolute;
    top: -28px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--surface-1);
    border: 1px solid var(--glass-border);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}
.bar-pill:hover .bar-tooltip {
    opacity: 1;
}
.bar-label {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 8px;
    font-weight: 500;
}
.balance-card {
    background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(59,130,246,0.1));
    border: 1px solid rgba(16,185,129,0.2);
}
</style>
@endpush

@section('content')
{{-- Financial Overview --}}
<div class="stats-grid">
    <div class="stat-card emerald">
        <div class="stat-icon">📥</div>
        <div class="stat-value">KSh {{ number_format($totalContributions) }}</div>
        <div class="stat-label">Total Contributions</div>
        <div class="stat-change up">Confirmed revenue</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">📤</div>
        <div class="stat-value">KSh {{ number_format($totalExpenses) }}</div>
        <div class="stat-label">Approved Expenses</div>
        <div class="stat-change down">Disbursed cash</div>
    </div>
    <div class="stat-card balance-card">
        <div class="stat-icon">⚖️</div>
        <div class="stat-value" style="color: {{ $netBalance >= 0 ? 'var(--emerald-400)' : 'var(--red-400)' }}">
            KSh {{ number_format(abs($netBalance)) }}
        </div>
        <div class="stat-label">Remaining Net Balance</div>
        <div class="stat-change {{ $netBalance >= 0 ? 'up' : 'down' }}">
            {{ $netBalance >= 0 ? 'Surplus Reserve Available' : 'Deficit Shortage Warning' }}
        </div>
    </div>
</div>

{{-- Monthly Expense Graph (Module 10 Requirement) --}}
<div class="expense-chart-container">
    <div class="chart-title">📈 Monthly Expense Graph <span class="text-xs text-muted">(Approved Totals)</span></div>
    @if(count($monthlyTotals))
        @php
            $maxVal = count($monthlyTotals) > 0 ? max(max($monthlyTotals), 1000) : 1000;
        @endphp
        <div class="visual-bar-chart">
            @foreach($monthlyTotals as $month => $total)
                @php
                    $pctHeight = min(100, ($total / $maxVal) * 100);
                    $formattedMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y');
                @endphp
                <div class="bar-col">
                    <div class="bar-pill" style="height: {{ $pctHeight }}%">
                        <div class="bar-tooltip">KSh {{ number_format($total) }}</div>
                    </div>
                    <div class="bar-label">{{ $formattedMonth }}</div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center; padding:40px; color:var(--text-muted); font-size:13px;">
            No historical data found to construct monthly graphs yet.
        </div>
    @endif
</div>

<div class="dashboard-grid">
    {{-- Left: Expenses table --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">💸 Expenses Log</span>
                @if(auth()->user()->hasRole(['admin', 'treasurer']))
                <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">+ Record Expense</a>
                @endif
            </div>
            <div class="table-wrap">
                @if($expenses->count())
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Approved</th>
                            @if(auth()->user()->hasRole(['admin', 'treasurer']))
                            <th style="text-align:right;">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $e)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</strong><br>
                                <span class="text-xs text-muted">Paid by: {{ $e->paidBy?->name }}</span>
                            </td>
                            <td>
                                <span style="font-weight:600;">{{ $e->description }}</span><br>
                                @if($e->receipt_url)
                                <a href="{{ asset('storage/' . $e->receipt_url) }}" target="_blank" class="text-xs text-emerald" style="text-decoration:underline;">
                                    View Receipt 📄
                                </a>
                                @else
                                <span class="text-xs text-muted">No Receipt Attached</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-red">KSh {{ number_format($e->amount) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-gray">{{ $e->getCategoryLabel() }}</span>
                            </td>
                            <td>
                                @if($e->is_approved)
                                <span class="badge badge-green">Approved</span>
                                @else
                                <span class="badge badge-yellow">Pending</span>
                                @endif
                            </td>
                            @if(auth()->user()->hasRole(['admin', 'treasurer']))
                            <td style="text-align:right; white-space: nowrap;">
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('expenses.approve', $e) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn {{ $e->is_approved ? 'btn-secondary' : 'btn-primary' }} btn-sm" style="padding:4px 8px;">
                                        {{ $e->is_approved ? 'Revoke' : 'Approve' }}
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('expenses.destroy', $e) }}" style="display:inline;" onsubmit="return confirm('Delete this expense?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">💸</div>
                    <div class="empty-state-title">No expenses recorded</div>
                </div>
                @endif
            </div>
            @if($expenses->hasPages())
            <div class="card-footer">
                {{ $expenses->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Right: Category Breakdown --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Category Breakdown</span>
            </div>
            <div class="card-body">
                @if($byCategory->count())
                <div style="display:flex; flex-direction:column; gap:16px;">
                    @foreach($byCategory as $cat)
                    <div>
                        <div class="d-flex justify-between mb-1" style="font-size:13px;">
                            @php
                                $displayLabel = match($cat->category) {
                                    'turf'         => 'Turf Hire',
                                    'equipment'    => 'Equipment',
                                    'refreshments' => 'Refreshments',
                                    'transport'    => 'Transport',
                                    'medical'      => 'Medical',
                                    default        => 'Miscellaneous',
                                };
                            @endphp
                            <span class="font-medium">{{ $displayLabel }}</span>
                            <strong>KSh {{ number_format($cat->total) }}</strong>
                        </div>
                        <div class="progress-bar-wrap">
                            @php
                                $pct = $totalExpenses > 0 ? ($cat->total / $totalExpenses) * 100 : 0;
                            @endphp
                            <div class="progress-bar gold" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-muted" style="margin-top:2px; display:block;">
                            {{ $cat->count }} transactions ({{ round($pct) }}% of total)
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state" style="padding:20px;">
                    <p class="text-xs text-muted">No approved transactions recorded.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
