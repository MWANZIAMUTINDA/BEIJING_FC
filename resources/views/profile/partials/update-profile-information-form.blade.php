<div class="profile-card">
    <div class="profile-card-title">Profile Information</div>
    <div class="profile-card-desc">Update your personal details and playing position.</div>

    @if(session('success') && !session('tab') || session('tab') === 'info')
    <div class="alert alert-success animate-in" style="margin-bottom:20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any() && !$errors->hasBag('userDeletion'))
    <div class="alert alert-error animate-in" style="margin-bottom:20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" id="profileInfoForm">
        @csrf
        @method('PATCH')

        {{-- Full Name + Username --}}
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="pi_name">Full Name <span style="color:var(--gold-400);">*</span></label>
                <input id="pi_name" type="text" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}" required placeholder="Your full name">
            </div>
            <div class="form-group">
                <label class="form-label" for="pi_username">Username <span style="color:var(--gold-400);">*</span></label>
                <div style="position:relative;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;">@</span>
                    <input id="pi_username" type="text" name="username" class="form-control"
                           style="padding-left:28px;"
                           value="{{ old('username', $user->username) }}" required placeholder="handle">
                </div>
            </div>
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label class="form-label" for="pi_phone">Phone Number <span style="color:var(--gold-400);">*</span></label>
            <input id="pi_phone" type="text" name="phone" class="form-control"
                   value="{{ old('phone', $user->phone) }}" required placeholder="e.g. 0712345678">
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="pi_email">
                Email
                <span style="color:var(--text-muted);font-weight:400;font-size:12px;">(optional — required for password reset)</span>
            </label>
            <input id="pi_email" type="email" name="email" class="form-control"
                   value="{{ old('email', $user->email) }}" placeholder="you@example.com">
            @if($user->email && !$user->email_verified_at)
            <p style="font-size:11px;color:#f59e0b;margin-top:5px;">
                ⚠ Email not verified.
                <a href="{{ route('verification.notice') }}" style="color:var(--emerald-400);">Resend verification</a>
            </p>
            @elseif($user->email && $user->email_verified_at)
            <p style="font-size:11px;color:#10b981;margin-top:5px;">✓ Email verified</p>
            @endif
        </div>

        {{-- Position + Role (read-only) --}}
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="pi_position">Playing Position <span style="color:var(--gold-400);">*</span></label>
                <select id="pi_position" name="position" class="form-control" required>
                    <option value="GK" {{ old('position', $user->position) === 'GK' ? 'selected' : '' }}>⚽ Goalkeeper (GK)</option>
                    <option value="DF" {{ old('position', $user->position) === 'DF' ? 'selected' : '' }}>🛡 Defender (DF)</option>
                    <option value="MF" {{ old('position', $user->position) === 'MF' ? 'selected' : '' }}>⚡ Midfielder (MF)</option>
                    <option value="FW" {{ old('position', $user->position) === 'FW' ? 'selected' : '' }}>🎯 Forward (FW)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Account Role</label>
                <div style="display:flex;align-items:center;height:42px;padding:0 14px;background:var(--surface-2);border:1px solid var(--border);border-radius:8px;gap:8px;">
                    <span class="badge badge-{{ $user->role_color }}">{{ $user->role_label }}</span>
                    <span style="font-size:12px;color:var(--text-muted);">(managed by Admin)</span>
                </div>
            </div>
        </div>

        <div class="profile-save-row">
            <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Changes
            </button>
        </div>
    </form>
</div>
