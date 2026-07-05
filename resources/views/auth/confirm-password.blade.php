@extends('layouts.guest')
@section('title', 'Confirm Password')
@section('subtitle', 'This area requires password confirmation')

@section('content')

<div style="text-align:center;margin-bottom:24px;">
    <div style="width:64px;height:64px;background:rgba(245,158,11,0.1);border:2px solid rgba(245,158,11,0.3);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
        <svg width="28" height="28" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
</div>

<p style="font-size:14px;color:rgba(255,255,255,0.5);line-height:1.7;margin-bottom:24px;text-align:center;">
    This is a secure area. Please confirm your password before continuing.
</p>

@if($errors->any())
<div class="auth-alert auth-alert-error" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<form method="POST" action="{{ route('password.confirm') }}" id="confirmForm">
    @csrf

    <div class="auth-form-group">
        <label class="auth-label" for="confirm_password">Password</label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </span>
            <input id="confirm_password" type="password" name="password"
                   class="auth-input has-right-icon {{ $errors->has('password') ? 'error' : '' }}"
                   placeholder="Enter your password" required autofocus autocomplete="current-password">
            <button type="button" class="auth-input-icon-right" id="cpwBtn"
                    onclick="togglePassword('confirm_password','cpwBtn')">
                <svg class="eye-open" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="eye-closed" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="auth-btn" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Confirm Password
    </button>
</form>
@endsection
