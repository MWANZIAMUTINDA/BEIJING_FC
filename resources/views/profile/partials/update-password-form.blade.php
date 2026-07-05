<div class="profile-card">
    <div class="profile-card-title">Change Password</div>
    <div class="profile-card-desc">Ensure your account uses a strong, unique password to keep it secure.</div>

    @if(session('status') === 'password-updated')
    <div class="alert alert-success animate-in" style="margin-bottom:20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Password updated successfully!
    </div>
    @endif

    @if($errors->hasBag('updatePassword'))
    <div class="alert alert-error animate-in" style="margin-bottom:20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $errors->updatePassword->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" id="updatePasswordForm">
        @csrf
        @method('PUT')

        {{-- Current Password --}}
        <div class="form-group">
            <label class="form-label" for="sec_current_password">Current Password <span style="color:var(--gold-400);">*</span></label>
            <div class="profile-pw-wrap">
                <input id="sec_current_password" type="password" name="current_password"
                       class="form-control" style="padding-right:42px;"
                       placeholder="Your current password" autocomplete="current-password">
                <button type="button" class="profile-pw-toggle" id="secPwBtn1"
                        onclick="profileTogglePw('sec_current_password','secPwBtn1')">
                    <svg class="eye-on" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-off" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- New Password --}}
        <div class="form-group">
            <label class="form-label" for="sec_password">New Password <span style="color:var(--gold-400);">*</span></label>
            <div class="profile-pw-wrap">
                <input id="sec_password" type="password" name="password"
                       class="form-control" style="padding-right:42px;"
                       placeholder="Min 8 characters" autocomplete="new-password">
                <button type="button" class="profile-pw-toggle" id="secPwBtn2"
                        onclick="profileTogglePw('sec_password','secPwBtn2')">
                    <svg class="eye-on" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-off" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            {{-- Strength --}}
            <div class="pw-strength" style="margin-top:8px;">
                <div class="pw-strength-bars" id="secPwBars">
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                    <div class="pw-strength-bar"></div>
                </div>
                <span class="pw-strength-label" id="secPwLabel"></span>
            </div>
        </div>

        {{-- Confirm New Password --}}
        <div class="form-group">
            <label class="form-label" for="sec_password_confirmation">Confirm New Password <span style="color:var(--gold-400);">*</span></label>
            <div class="profile-pw-wrap">
                <input id="sec_password_confirmation" type="password" name="password_confirmation"
                       class="form-control" style="padding-right:42px;"
                       placeholder="Repeat new password" autocomplete="new-password">
                <button type="button" class="profile-pw-toggle" id="secPwBtn3"
                        onclick="profileTogglePw('sec_password_confirmation','secPwBtn3')">
                    <svg class="eye-on" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-off" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="profile-save-row">
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Update Password
            </button>
        </div>
    </form>
</div>

<script>
// Inline strength meter for profile page (uses global pw helpers if available)
(function() {
    const pwInput = document.getElementById('sec_password');
    const bars    = document.querySelectorAll('#secPwBars .pw-strength-bar');
    const label   = document.getElementById('secPwLabel');
    if (!pwInput) return;

    function score(pw) {
        let s = 0;
        if (pw.length >= 8) s++;
        if (pw.length >= 12) s++;
        if (/[A-Z]/.test(pw)) s++;
        if (/[0-9]/.test(pw)) s++;
        if (/[^A-Za-z0-9]/.test(pw)) s++;
        return s;
    }

    pwInput.addEventListener('input', () => {
        const s = score(pwInput.value);
        const cls = ['','weak','fair','good','strong','strong'][s];
        const lbl = ['','Weak','Fair','Good','Strong','Very Strong'][s];
        bars.forEach((b, i) => {
            b.className = 'pw-strength-bar';
            if (i < s) b.classList.add('active-' + cls);
        });
        label.textContent = pwInput.value ? lbl : '';
    });
})();
</script>
