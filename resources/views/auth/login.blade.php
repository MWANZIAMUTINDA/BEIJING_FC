@extends('layouts.guest')
@section('title', 'Sign In')
@section('subtitle', 'Welcome back — sign in to continue')

@section('content')

{{-- Error Alert --}}
@if($errors->any())
<div class="auth-alert auth-alert-error" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

{{-- Status (e.g. password reset success) --}}
@if(session('status'))
<div class="auth-alert auth-alert-success" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ session('status') }}</span>
</div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    {{-- Email / Username / Phone --}}
    <div class="auth-form-group">
        <label class="auth-label" for="login_email">Username, Email or Phone</label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            <input id="login_email" type="text" name="email" class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email') }}" placeholder="Enter username, email or phone" required autofocus autocomplete="username">
        </div>
    </div>

    {{-- Password --}}
    <div class="auth-form-group">
        <label class="auth-label" for="login_password">Password</label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input id="login_password" type="password" name="password"
                   class="auth-input has-right-icon {{ $errors->has('password') ? 'error' : '' }}"
                   placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="auth-input-icon-right" id="pwToggleBtn"
                    onclick="togglePassword('login_password','pwToggleBtn')" title="Show/hide password">
                <svg class="eye-open" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="eye-closed" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Remember + Forgot --}}
    <div class="auth-checkbox-row">
        <label class="auth-checkbox-label">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Remember me
        </label>
        @if(Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="auth-forgot-link">Forgot password?</a>
        @endif
    </div>

    <button type="submit" class="auth-btn" id="loginSubmitBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Sign In
    </button>
</form>

@if(Route::has('register'))
<p class="auth-footer-link">
    No account yet? <a href="{{ route('register') }}">Create one here</a>
</p>
@endif

<script>
// Show loading state on submit
document.getElementById('loginForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('loginSubmitBtn');
    btn.innerHTML = '<svg width="16" height="16" style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Signing In...';
    btn.disabled = true;
});
</script>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
