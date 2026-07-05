@extends('layouts.app')
@section('title', 'Payment Receipt #{{ $payment->id }}')
@section('page-title', 'Payment Receipt')

@push('styles')
<style>
.receipt-wrap {
    max-width: 560px;
    margin: 0 auto;
}
.receipt-card {
    background: var(--navy-800);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    overflow: hidden;
}
.receipt-header {
    background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, transparent 100%);
    padding: 32px 36px 24px;
    border-bottom: 1px dashed var(--glass-border);
    text-align: center;
}
.receipt-logo {
    font-family:'Outfit',sans-serif; font-weight:900; font-size:28px;
    color: var(--emerald-400); letter-spacing: 2px; margin-bottom: 4px;
}
.receipt-org { font-size:12px; color:var(--text-muted); letter-spacing:.5px; }
.receipt-title {
    margin-top: 20px;
    font-family:'Outfit',sans-serif; font-weight:800; font-size:18px;
    color:var(--text-primary);
}
.receipt-ref { font-size:12px; color:var(--text-muted); margin-top:4px; font-family:monospace; }
.receipt-amount-block {
    padding: 28px 36px;
    border-bottom: 1px dashed var(--glass-border);
    text-align: center;
}
.receipt-amount {
    font-family:'Outfit',sans-serif; font-weight:900; font-size:48px;
    color: var(--emerald-400); line-height: 1;
}
.receipt-amount-label { font-size:12px; color:var(--text-muted); margin-top:6px; text-transform:uppercase; letter-spacing:1px; }
.receipt-rows { padding: 24px 36px; }
.receipt-row {
    display:flex; justify-content:space-between; align-items:center;
    padding: 10px 0; border-bottom: 1px solid var(--glass-border);
    font-size: 13px;
}
.receipt-row:last-child { border-bottom: none; }
.receipt-row-label { color:var(--text-muted); font-weight:500; }
.receipt-row-value { font-weight:700; color:var(--text-primary); text-align:right; }
.receipt-footer {
    padding: 20px 36px;
    background: rgba(16,185,129,0.04);
    border-top: 1px dashed var(--glass-border);
    text-align: center;
}
.receipt-status-seal {
    display:inline-flex; align-items:center; gap:8px;
    background: rgba(16,185,129,0.12); border: 2px solid var(--emerald-400);
    border-radius: 50px; padding: 8px 20px;
    font-family:'Outfit',sans-serif; font-weight:800; font-size:14px;
    color: var(--emerald-400); letter-spacing: 1px;
}

/* Print-specific overrides */
@media print {
    .sidebar, .topbar, .btn, nav, .breadcrumb, .app-header { display:none !important; }
    .main-content, .content-area, body, html { background:#fff !important; padding:0 !important; margin:0 !important; }
    .receipt-card {
        background: #fff !important; border: 1px solid #ddd !important;
        border-radius: 8px; color: #000 !important;
    }
    .receipt-logo, .receipt-amount { color: #16a34a !important; }
    .receipt-header { background: #f0fdf4 !important; }
    .receipt-footer { background: #f0fdf4 !important; }
    .receipt-row-label { color: #666 !important; }
    .receipt-row-value, .receipt-title { color: #000 !important; }
    .receipt-status-seal { border-color: #16a34a !important; color: #16a34a !important; background: #f0fdf4 !important; }
    .print-actions { display:none !important; }
}
</style>
@endpush

@section('content')
<div class="receipt-wrap">

    {{-- Actions Bar --}}
    <div class="print-actions" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">← Back</a>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print Receipt</button>
            <a href="{{ route('payments.statement') }}" class="btn btn-secondary btn-sm">📋 My Statement</a>
        </div>
    </div>

    <div class="receipt-card">
        {{-- Header --}}
        <div class="receipt-header">
            <div class="receipt-logo">BFC</div>
            <div class="receipt-org">Beijing FC — Official Payment Receipt</div>
            <div class="receipt-title">
                @if($payment->isConfirmed()) ✅ Payment Confirmed
                @elseif($payment->status === 'pending') ⏳ Payment Pending
                @else ❌ Payment {{ ucfirst($payment->status) }}
                @endif
            </div>
            <div class="receipt-ref">REF: {{ $payment->mpesa_code ?? "REC-{$payment->id}" }}</div>
        </div>

        {{-- Amount Block --}}
        <div class="receipt-amount-block">
            <div class="receipt-amount">KSh {{ number_format($payment->amount) }}</div>
            <div class="receipt-amount-label">{{ $payment->getTypeLabel() }}</div>
        </div>

        {{-- Details --}}
        <div class="receipt-rows">
            <div class="receipt-row">
                <span class="receipt-row-label">Member Name</span>
                <span class="receipt-row-value">{{ $payment->user?->name ?? 'Unknown' }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Payment Type</span>
                <span class="receipt-row-value">{{ $payment->getTypeLabel() }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Phone Number</span>
                <span class="receipt-row-value" style="font-family:monospace;">{{ $payment->phone }}</span>
            </div>
            @if($payment->mpesa_code)
            <div class="receipt-row">
                <span class="receipt-row-label">M-Pesa Code</span>
                <span class="receipt-row-value" style="font-family:monospace;color:var(--emerald-400);">{{ $payment->mpesa_code }}</span>
            </div>
            @endif
            @if($payment->mpesa_receipt_number)
            <div class="receipt-row">
                <span class="receipt-row-label">M-Pesa Receipt</span>
                <span class="receipt-row-value" style="font-family:monospace;">{{ $payment->mpesa_receipt_number }}</span>
            </div>
            @endif
            @if($payment->match)
            <div class="receipt-row">
                <span class="receipt-row-label">Match</span>
                <span class="receipt-row-value">vs {{ $payment->match->opponent }} ({{ $payment->match->formatted_date }})</span>
            </div>
            @endif
            <div class="receipt-row">
                <span class="receipt-row-label">Date Recorded</span>
                <span class="receipt-row-value">{{ $payment->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Recorded By</span>
                <span class="receipt-row-value">{{ $payment->recordedBy?->name ?? 'System' }}</span>
            </div>
            @if($payment->notes)
            <div class="receipt-row">
                <span class="receipt-row-label">Notes</span>
                <span class="receipt-row-value" style="font-style:italic;max-width:260px;text-align:right;">{{ $payment->notes }}</span>
            </div>
            @endif
        </div>

        {{-- Footer Seal --}}
        <div class="receipt-footer">
            <div class="receipt-status-seal">
                @if($payment->isConfirmed()) ✅ OFFICIAL RECEIPT
                @elseif($payment->status === 'pending') ⏳ PENDING
                @else ⚠️ {{ strtoupper($payment->status) }}
                @endif
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:14px;">
                This is an official receipt from Beijing FC Management System.<br>
                Printed on: {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
    </div>
</div>
@endsection
