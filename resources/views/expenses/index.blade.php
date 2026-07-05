@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Club Expenses')

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
    <div class="stat-card {{ $netBalance >= 0 ? 'emerald' : 'red' }}">
        <div class="stat-icon">{{ $netBalance >= 0 ? '✓' : '⚠️' }}</div>
        <div class="stat-value">KSh {{ number_format(abs($netBalance)) }}</div>
        <div class="stat-label">Net Club Balance</div>
        <div class="stat-change {{ $netBalance >= 0 ? 'up' : 'down' }}">
            {{ $netBalance >= 0 ? 'In credit surplus' : 'In deficit shortage' }}
        </div>
    </div>
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
                                <span class="text-xs text-muted">No Receipt</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-red">KSh {{ number_format($e->amount) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-gray">{{ ucfirst(str_replace('_', ' ', $e->category)) }}</span>
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
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $cat->category)) }}</span>
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
