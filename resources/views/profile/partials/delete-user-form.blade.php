<div class="danger-card">
    <div class="danger-card-title">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        Delete Account
    </div>
    <div class="danger-card-desc">
        Once your account is deleted, all your data will be permanently removed. This action <strong style="color:#f87171;">cannot be undone</strong>.
        Before deleting, please download any data you wish to retain.
    </div>

    @if($errors->hasBag('userDeletion'))
    <div class="alert alert-error animate-in" style="margin-bottom:20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $errors->userDeletion->first() }}
    </div>
    @endif

    {{-- Confirm Delete Toggle --}}
    <div id="deleteToggle">
        <button type="button" class="btn" id="deleteToggleBtn"
                style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171;width:100%;justify-content:center;padding:11px;"
                onclick="document.getElementById('deleteForm').style.display='block';this.style.display='none';">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            I want to delete my account
        </button>
    </div>

    <form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm" style="display:none;">
        @csrf
        @method('DELETE')

        <div class="form-group">
            <label class="form-label" for="del_password">
                Enter your password to confirm deletion
            </label>
            <div class="profile-pw-wrap">
                <input id="del_password" type="password" name="password" class="form-control"
                       style="border-color:rgba(239,68,68,0.4);padding-right:42px;"
                       placeholder="Your current password" required>
                <button type="button" class="profile-pw-toggle" id="delPwBtn"
                        onclick="profileTogglePw('del_password','delPwBtn')">
                    <svg class="eye-on" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg class="eye-off" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="button"
                    style="flex:1;padding:11px;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:8px;color:var(--text-muted);font-size:13px;font-family:inherit;cursor:pointer;"
                    onclick="document.getElementById('deleteForm').style.display='none';document.getElementById('deleteToggleBtn').style.display='flex';">
                Cancel
            </button>
            <button type="submit"
                    style="flex:1;padding:11px;background:linear-gradient(135deg,#dc2626,#b91c1c);border:none;border-radius:8px;color:white;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Permanently Delete
            </button>
        </div>
    </form>

    @if($errors->hasBag('userDeletion'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('deleteForm').style.display = 'block';
            document.getElementById('deleteToggleBtn').style.display = 'none';
        });
    </script>
    @endif
</div>
