@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('subtitle', 'Reset your account password')

@section('content')

@if(session('status'))
<div class="auth-alert auth-alert-success" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ session('status') }}</span>
</div>
@endif

@if($errors->any())
<div class="auth-alert auth-alert-error" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<p style="font-size:14px;color:rgba(255,255,255,0.5);line-height:1.7;margin-bottom:24px;">
    Forgot your password? Enter your email address below and we'll send you a password reset link.
</p>

<form method="POST" action="{{ route('password.email') }}" id="forgotForm">
    @csrf

    <div class="auth-form-group">
        <label class="auth-label" for="forgot_email">Email Address <span class="req">*</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input id="forgot_email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
        </div>
    </div>

    <button type="submit" class="auth-btn" id="forgotBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Send Reset Link
    </button>
</form>

<div class="auth-note">
    <strong>⚠ No email on your account?</strong><br>
    If you registered without an email address, you cannot use this reset flow. Please contact an <strong>Admin</strong> to reset your password manually.
</div>

<p class="auth-footer-link" style="margin-top:18px;">
    Remembered it? <a href="{{ route('login') }}">Back to Sign In</a>
</p>

<script>
document.getElementById('forgotForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('forgotBtn');
    btn.innerHTML = '<svg width="16" height="16" style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sending...';
    btn.disabled = true;
});
</script>
@endsection
