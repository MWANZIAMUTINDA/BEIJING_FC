@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('breadcrumb', 'Account / Profile')

@section('content')
<div class="profile-page">

    {{-- ═══ Profile Header Card ══════════════════════════════════════════════ --}}
    <div class="profile-hero-card">
        <div class="profile-hero-bg"></div>
        <div class="profile-hero-content">
            {{-- Avatar --}}
            <div class="profile-avatar-wrap" id="avatarWrap">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="profile-avatar-img" id="avatarImg">
                <label class="profile-avatar-edit" for="avatarInput" title="Change photo">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </label>
            </div>

            {{-- Info --}}
            <div class="profile-hero-info">
                <div class="profile-hero-name">{{ $user->name }}</div>
                <div class="profile-hero-meta">
                    <span class="badge badge-{{ $user->role_color }}" style="font-size:11px;padding:3px 10px;">
                        {{ $user->role_label }}
                    </span>
                    <span class="profile-hero-pos">{{ $user->positionLabel() }}</span>
                    <span class="profile-hero-username">@{{ $user->username }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden avatar upload form (auto-submits on file change) --}}
    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
          id="avatarForm" style="display:none;">
        @csrf
        <input type="file" id="avatarInput" name="avatar" accept="image/*"
               onchange="previewAndSubmitAvatar(this)">
    </form>

    {{-- ═══ Tab Navigation ═════════════════════════════════════════════════ --}}
    <div class="profile-tabs" id="profileTabs">
        <button class="profile-tab active" onclick="switchTab('info',this)" id="tab-info">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile Info
        </button>
        <button class="profile-tab" onclick="switchTab('security',this)" id="tab-security">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Security
        </button>
        <button class="profile-tab profile-tab-danger" onclick="switchTab('danger',this)" id="tab-danger">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Danger Zone
        </button>
    </div>

    {{-- ═══ Tab Panels ════════════════════════════════════════════════════= --}}

    {{-- Tab: Profile Info --}}
    <div class="profile-panel active" id="panel-info">
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Tab: Security --}}
    <div class="profile-panel" id="panel-security">
        @include('profile.partials.update-password-form')
    </div>

    {{-- Tab: Danger Zone --}}
    <div class="profile-panel" id="panel-danger">
        @include('profile.partials.delete-user-form')
    </div>

</div>

@push('styles')
<style>
/* ── Profile Page ──────────────────────────────────────────────────────────── */
.profile-page { max-width: 780px; margin: 0 auto; }

/* Hero card */
.profile-hero-card {
    position: relative;
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
    padding: 28px 32px;
}
.profile-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, transparent 60%);
    pointer-events: none;
}
.profile-hero-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 24px;
}
@media (max-width: 480px) {
    .profile-hero-content { flex-direction: column; text-align: center; }
}

/* Avatar */
.profile-avatar-wrap {
    position: relative;
    flex-shrink: 0;
    cursor: pointer;
}
.profile-avatar-img {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(16,185,129,0.4);
    display: block;
    transition: filter 0.2s;
}
.profile-avatar-wrap:hover .profile-avatar-img { filter: brightness(0.7); }
.profile-avatar-edit {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,0,0,0.3);
    color: white;
    opacity: 0;
    cursor: pointer;
    transition: opacity 0.2s;
}
.profile-avatar-wrap:hover .profile-avatar-edit { opacity: 1; }

/* Info */
.profile-hero-name {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}
.profile-hero-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.profile-hero-pos, .profile-hero-username {
    font-size: 13px;
    color: var(--text-muted);
}
.profile-hero-username { color: var(--emerald-400); }

/* Tabs */
.profile-tabs {
    display: flex;
    gap: 4px;
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 20px;
}
.profile-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 12px;
    border: none;
    background: transparent;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.profile-tab:hover { color: var(--text-secondary); background: rgba(255,255,255,0.04); }
.profile-tab.active {
    background: rgba(16,185,129,0.12);
    color: var(--emerald-400);
    border: 1px solid rgba(16,185,129,0.2);
}
.profile-tab-danger.active {
    background: rgba(239,68,68,0.1);
    color: #f87171;
    border-color: rgba(239,68,68,0.2);
}
.profile-tab-danger:hover { color: #f87171; }
@media (max-width: 480px) {
    .profile-tab span { display: none; }
    .profile-tab { padding: 10px; }
}

/* Panels */
.profile-panel { display: none; }
.profile-panel.active { display: block; }

/* Form card */
.profile-card {
    background: var(--surface-1);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px 32px;
}
.profile-card-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}
.profile-card-desc {
    font-size: 13px;
    color: var(--text-muted);
    margin-bottom: 24px;
    line-height: 1.6;
}
.profile-divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 24px 0;
}
.profile-save-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

/* Danger zone card */
.danger-card {
    background: rgba(239,68,68,0.05);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: 16px;
    padding: 28px 32px;
}
.danger-card-title {
    font-size: 15px;
    font-weight: 600;
    color: #f87171;
    margin-bottom: 4px;
}
.danger-card-desc {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    margin-bottom: 20px;
    line-height: 1.6;
}

/* Password visibility button in profile forms */
.profile-pw-wrap { position: relative; }
.profile-pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0;
    display: flex;
    align-items: center;
    transition: color 0.2s;
}
.profile-pw-toggle:hover { color: var(--text-secondary); }
</style>
@endpush

@push('scripts')
<script>
// Tab switching
function switchTab(name, btn) {
    document.querySelectorAll('.profile-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    btn.classList.add('active');
}

// Open correct tab if there's a success message from that section
@if(session('tab'))
const tabToOpen = '{{ session('tab') }}';
const tabBtn = document.getElementById('tab-' + tabToOpen);
if (tabBtn) switchTab(tabToOpen, tabBtn);
@endif

// Avatar preview before upload
function previewAndSubmitAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarImg').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('avatarForm').submit();
}

// Toggle password visibility in profile forms
function profileTogglePw(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    if (!input || !btn) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-on').style.display  = isHidden ? 'none' : '';
    btn.querySelector('.eye-off').style.display = isHidden ? '' : 'none';
}
</script>
@endpush
@endsection
