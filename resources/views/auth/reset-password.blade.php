@extends('layouts.guest')
@section('title', 'Reset Password')
@section('subtitle', 'Create a new password for your account')

@section('content')

@if($errors->any())
<div class="auth-alert auth-alert-error" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<form method="POST" action="{{ route('password.store') }}" id="resetForm">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    {{-- Email --}}
    <div class="auth-form-group">
        <label class="auth-label" for="reset_email">Email Address <span class="req">*</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input id="reset_email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email', $request->email) }}" placeholder="you@example.com" required autofocus autocomplete="username">
        </div>
    </div>

    {{-- New Password --}}
    <div class="auth-form-group">
        <label class="auth-label" for="reset_password">New Password <span class="req">*</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input id="reset_password" type="password" name="password"
                   class="auth-input has-right-icon {{ $errors->has('password') ? 'error' : '' }}"
                   placeholder="Min 8 characters" required autocomplete="new-password">
            <button type="button" class="auth-input-icon-right" id="rpwBtn1"
                    onclick="togglePassword('reset_password','rpwBtn1')">
                <svg class="eye-open" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="eye-closed" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
        <div class="pw-strength">
            <div class="pw-strength-bars" id="rpwBars">
                <div class="pw-strength-bar"></div>
                <div class="pw-strength-bar"></div>
                <div class="pw-strength-bar"></div>
                <div class="pw-strength-bar"></div>
            </div>
            <span class="pw-strength-label" id="rpwLabel"></span>
        </div>
    </div>

    {{-- Confirm Password --}}
    <div class="auth-form-group">
        <label class="auth-label" for="reset_password_confirmation">Confirm New Password <span class="req">*</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </span>
            <input id="reset_password_confirmation" type="password" name="password_confirmation"
                   class="auth-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                   placeholder="Repeat new password" required autocomplete="new-password">
        </div>
    </div>

    <button type="submit" class="auth-btn" id="resetBtn" style="margin-top:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Reset Password
    </button>
</form>

<p class="auth-footer-link" style="margin-top:20px;">
    Back to <a href="{{ route('login') }}">Sign In</a>
</p>

<script>
updateStrength('reset_password', 'rpwBars', 'rpwLabel');
document.getElementById('resetForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('resetBtn');
    btn.innerHTML = '<svg width="16" height="16" style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Resetting...';
    btn.disabled = true;
});
</script>
@endsection
