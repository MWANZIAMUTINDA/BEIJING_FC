@extends('layouts.app')
@section('title', 'Record Payment')
@section('page-title', 'Record Payment')
@section('breadcrumb')
<a href="{{ route('payments.index') }}">Payments</a> / Record
@endsection

@push('styles')
<style>
.type-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:4px; }
.type-card {
    border: 2px solid var(--glass-border);
    border-radius: var(--radius-md);
    padding: 14px 16px;
    cursor: pointer;
    transition: var(--transition);
    background: var(--navy-750);
}
.type-card:hover { border-color: rgba(255,255,255,.2); background: var(--glass-hover); }
.type-card.selected-monthly { border-color: var(--blue-400);    background: rgba(59,130,246,.1); }
.type-card.selected-match   { border-color: var(--emerald-400); background: rgba(16,185,129,.1); }
.type-card.selected-partial { border-color: var(--gold-400);    background: rgba(245,158,11,.1); }
.type-card.selected-penalty { border-color: var(--red-400);     background: rgba(239,68,68,.1); }
.type-card-title { font-weight:700; font-size:13px; color:var(--text-primary); }
.type-card-amount { font-family:'Outfit',sans-serif; font-weight:900; font-size:18px; margin-top:4px; }
.type-card.selected-monthly .type-card-amount { color:var(--blue-400); }
.type-card.selected-match   .type-card-amount { color:var(--emerald-400); }
.type-card.selected-partial .type-card-amount { color:var(--gold-400); }
.type-card.selected-penalty .type-card-amount { color:var(--red-400); }
.type-card-desc { font-size:10px; color:var(--text-muted); margin-top:3px; }
</style>
@endpush

@section('content')
<div class="card" style="max-width: 640px; margin: 0 auto;">
    <div class="card-header">
        <span class="card-title">📝 Record Member Payment</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf

            {{-- Member --}}
            <div class="form-group">
                <label class="form-label" for="user_id">Select Member <span class="required">*</span></label>
                <select name="user_id" id="user_id" class="form-control @error('user_id') error @enderror" required onchange="prefillPhone()">
                    <option value="">— Choose Member —</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" data-phone="{{ $m->phone }}" data-billing="{{ $m->billing_type }}"
                        {{ old('user_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->name }} ({{ $m->phone }})
                        @if($m->jersey_number) — #{{ $m->jersey_number }} @endif
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Payment Type Cards --}}
            <div class="form-group">
                <label class="form-label">Payment Type <span class="required">*</span></label>
                <input type="hidden" name="type" id="type_input" value="{{ old('type', 'monthly') }}">
                <div class="type-cards">
                    <div class="type-card" id="card-monthly" onclick="selectType('monthly', 2080)">
                        <div class="type-card-title">📅 Monthly</div>
                        <div class="type-card-amount">KSh 2,080</div>
                        <div class="type-card-desc">Fixed monthly subscription</div>
                    </div>
                    <div class="type-card" id="card-match" onclick="selectType('match', 350)">
                        <div class="type-card-title">⚽ Match Fee</div>
                        <div class="type-card-amount">KSh 350</div>
                        <div class="type-card-desc">Per match played</div>
                    </div>
                    <div class="type-card" id="card-partial" onclick="selectType('partial', 0)">
                        <div class="type-card-title">💸 Partial</div>
                        <div class="type-card-amount">Any Amount</div>
                        <div class="type-card-desc">Partial contribution</div>
                    </div>
                </div>
                @error('type')
                <div class="form-error" style="margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Match relation --}}
            <div class="form-group" id="match_group" style="display: none;">
                <label class="form-label" for="match_id">Link to Match (Optional)</label>
                <select name="match_id" id="match_id" class="form-control @error('match_id') error @enderror">
                    <option value="">— Choose Match —</option>
                    @foreach($matches as $match)
                    <option value="{{ $match->id }}" {{ old('match_id') == $match->id ? 'selected' : '' }}>
                        {{ $match->formatted_date }} vs {{ $match->opponent }} (KSh {{ number_format($match->match_fee) }})
                    </option>
                    @endforeach
                </select>
                @error('match_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid">
                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (KSh) <span class="required">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', 2080) }}" min="1"
                        class="form-control @error('amount') error @enderror"
                        style="font-family:'Outfit',sans-serif;font-weight:700;font-size:16px;" required>
                    <div class="form-hint" id="amount_hint">Fixed monthly contribution</div>
                    @error('amount')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="form-control @error('phone') error @enderror" placeholder="e.g. 0712345678" required>
                    @error('phone')
                    <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- M-Pesa Ref --}}
            <div class="form-group">
                <label class="form-label" for="mpesa_code">M-Pesa Reference Code</label>
                <input type="text" name="mpesa_code" id="mpesa_code" value="{{ old('mpesa_code') }}"
                    class="form-control @error('mpesa_code') error @enderror"
                    placeholder="e.g. SKL892HJ30" style="text-transform:uppercase;font-family:monospace;letter-spacing:.5px;">
                <span class="form-hint">Enter the M-Pesa transaction code from the SMS confirmation.</span>
                @error('mpesa_code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label" for="notes">Additional Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="e.g. November 2025 contribution">{{ old('notes') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-between" style="margin-top: 24px;">
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">✓ Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const typeAmounts = { monthly: 2080, match: 350, partial: 0, penalty: 0 };
const typeHints = {
    monthly: 'Fixed monthly subscription (KSh 2,080)',
    match:   'Standard match fee (KSh 350)',
    partial: 'Enter any partial amount',
    penalty: 'Enter penalty fee amount',
};

function selectType(type, amount) {
    document.getElementById('type_input').value = type;
    document.getElementById('match_group').style.display = type === 'match' ? 'block' : 'none';
    if (type !== 'match') document.getElementById('match_id').value = '';
    if (amount > 0) document.getElementById('amount').value = amount;
    document.getElementById('amount_hint').textContent = typeHints[type];
    document.getElementById('amount').readOnly = (type === 'monthly' || type === 'match');

    // Update card styling
    ['monthly','match','partial','penalty'].forEach(t => {
        const card = document.getElementById('card-' + t);
        if (card) {
            card.className = 'type-card' + (t === type ? ' selected-' + t : '');
        }
    });
}

function prefillPhone() {
    const sel = document.getElementById('user_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.phone) {
        document.getElementById('phone').value = opt.dataset.phone;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    selectType('{{ old('type', 'monthly') }}', {{ old('amount', 2080) }});
});
</script>
@endpush
