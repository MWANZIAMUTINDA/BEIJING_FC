@extends('layouts.guest')
@section('title', 'Create Account')
@section('subtitle', 'Join Beijing FC Management System')

@section('content')

@if($errors->any())
<div class="auth-alert auth-alert-error" role="alert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    {{-- Full Name + Username --}}
    <div class="auth-grid-2">
        <div class="auth-form-group">
            <label class="auth-label" for="reg_name">Full Name <span class="req">*</span></label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input id="reg_name" type="text" name="name" class="auth-input {{ $errors->has('name') ? 'error' : '' }}"
                       value="{{ old('name') }}" placeholder="Your full name" required autofocus>
            </div>
        </div>
        <div class="auth-form-group">
            <label class="auth-label" for="reg_username">Username <span class="req">*</span></label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">@</span>
                <input id="reg_username" type="text" name="username" class="auth-input {{ $errors->has('username') ? 'error' : '' }}"
                       value="{{ old('username') }}" placeholder="unique_handle" required>
            </div>
        </div>
    </div>

    {{-- Phone --}}
    <div class="auth-form-group">
        <label class="auth-label" for="reg_phone">Phone Number <span class="req">*</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </span>
            <input id="reg_phone" type="text" name="phone" class="auth-input {{ $errors->has('phone') ? 'error' : '' }}"
                   value="{{ old('phone') }}" placeholder="e.g. 0712345678" required>
        </div>
    </div>

    {{-- Email (optional) --}}
    <div class="auth-form-group">
        <label class="auth-label" for="reg_email">Email <span style="color:rgba(255,255,255,0.3);font-weight:400;">(optional)</span></label>
        <div class="auth-input-wrap">
            <span class="auth-input-icon">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input id="reg_email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email') }}" placeholder="you@example.com">
        </div>
    </div>

    {{-- Position + Role (read only) --}}
    <div class="auth-grid-2">
        <div class="auth-form-group">
            <label class="auth-label" for="reg_position">Position <span class="req">*</span></label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </span>
                <select id="reg_position" name="position" class="auth-input {{ $errors->has('position') ? 'error' : '' }}" required>
                    <option value="">Select position</option>
                    <option value="GK" {{ old('position')=='GK' ? 'selected' : '' }}>⚽ Goalkeeper</option>
                    <option value="DF" {{ old('position')=='DF' ? 'selected' : '' }}>🛡 Defender</option>
                    <option value="MF" {{ old('position')=='MF' ? 'selected' : '' }}>⚡ Midfielder</option>
                    <option value="FW" {{ old('position')=='FW' ? 'selected' : '' }}>🎯 Forward</option>
                </select>
            </div>
        </div>
        <div class="auth-form-group">
            <label class="auth-label">Account Role</label>
            <div style="padding:11px 14px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;">
                <span class="auth-role-badge">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Member
                </span>
            </div>
        </div>
    </div>

    {{-- Password --}}
    <div class="auth-grid-2">
        <div class="auth-form-group">
            <label class="auth-label" for="reg_password">Password <span class="req">*</span></label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input id="reg_password" type="password" name="password"
                       class="auth-input has-right-icon {{ $errors->has('password') ? 'error' : '' }}"
                       placeholder="Min 8 chars" required autocomplete="new-password">
                <button type="button" class="auth-input-icon-right" id="pwBtn1"
                        onclick="togglePassword('reg_password','pwBtn1')">
                    <svg class="eye-open" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-closed" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            {{-- Strength meter --}}
            <div class="pw-strength" id="pwStrength">
                <div class="pw-strength-bars" id="pwBars">
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                </div>
                <span class="pw-strength-label" id="pwLabel"></span>
            </div>
        </div>
        <div class="auth-form-group">
            <label class="auth-label" for="reg_password_confirmation">Confirm <span class="req">*</span></label>
            <div class="auth-input-wrap">
                <span class="auth-input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                <input id="reg_password_confirmation" type="password" name="password_confirmation"
                       class="auth-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                       placeholder="Repeat password" required autocomplete="new-password">
            </div>
        </div>
    </div>

    <button type="submit" class="auth-btn" id="regSubmitBtn" style="margin-top:4px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Create Account
    </button>
</form>

<p class="auth-footer-link">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</p>

<script>
updateStrength('reg_password', 'pwBars', 'pwLabel');
document.getElementById('registerForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('regSubmitBtn');
    btn.innerHTML = '<svg width="16" height="16" style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Creating Account...';
    btn.disabled = true;
});
</script>
@endsection
