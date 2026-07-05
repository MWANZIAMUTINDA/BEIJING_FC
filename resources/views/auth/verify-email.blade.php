@extends('layouts.guest')
@section('title', 'Verify Email')
@section('subtitle', 'One more step — verify your email address')

@section('content')

<div style="text-align:center;margin-bottom:28px;">
    <div style="width:72px;height:72px;background:rgba(16,185,129,0.1);border:2px solid rgba(16,185,129,0.3);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
        <svg width="32" height="32" fill="none" stroke="#10b981" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
</div>

@if(session('status') == 'verification-link-sent')
<div class="auth-alert auth-alert-success" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>A new verification link has been sent to your email address.</span>
</div>
@endif

<p style="font-size:14px;color:rgba(255,255,255,0.5);line-height:1.7;margin-bottom:28px;text-align:center;">
    Thanks for signing up! Before getting started, please verify your email address by clicking the link we emailed to you.
    If you didn't receive the email, we'll gladly send another.
</p>

<form method="POST" action="{{ route('verification.send') }}" id="verifyForm">
    @csrf
    <button type="submit" class="auth-btn" id="verifyBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Resend Verification Email
    </button>
</form>

<hr class="auth-divider">

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" style="width:100%;padding:11px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:10px;color:rgba(255,255,255,0.5);font-size:14px;font-family:inherit;cursor:pointer;transition:background 0.2s;">
        Sign Out
    </button>
</form>

<script>
document.getElementById('verifyForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('verifyBtn');
    btn.innerHTML = '<svg width="16" height="16" style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sending...';
    btn.disabled = true;
});
</script>
@endsection
