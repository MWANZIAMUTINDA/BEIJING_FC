<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sign In') — Beijing FC</title>
    <meta name="description" content="Beijing FC Management System — @yield('title', 'Sign In')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Auth Split-Screen Layout ───────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--navy-950, #050d1a);
            min-height: 100vh;
            display: flex;
        }

        .auth-split {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── Left Hero Panel ───────────────────────────────────── */
        .auth-hero {
            display: none;
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, #0a1628 0%, #0d2137 40%, #0a3320 100%);
            overflow: hidden;
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (min-width: 960px) {
            .auth-hero { display: flex; }
        }

        /* Animated mesh background */
        .auth-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 30%, rgba(16,185,129,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 60% at 80% 70%, rgba(245,158,11,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 80% 80% at 50% 50%, rgba(10,19,40,0.8) 0%, transparent 100%);
            pointer-events: none;
        }

        /* Floating orbs */
        .auth-hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            animation: floatOrb 8s ease-in-out infinite;
        }
        .auth-hero-orb-1 {
            width: 300px; height: 300px;
            background: rgba(16,185,129,0.15);
            top: -80px; left: -60px;
            animation-delay: 0s;
        }
        .auth-hero-orb-2 {
            width: 200px; height: 200px;
            background: rgba(245,158,11,0.12);
            bottom: 100px; right: -40px;
            animation-delay: -3s;
        }
        .auth-hero-orb-3 {
            width: 150px; height: 150px;
            background: rgba(16,185,129,0.1);
            bottom: -30px; left: 30%;
            animation-delay: -5s;
        }
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-20px) scale(1.05); }
        }

        .auth-hero-content {
            position: relative;
            z-index: 1;
        }

        .auth-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #34d399;
            letter-spacing: 0.5px;
            margin-bottom: 32px;
        }
        .auth-hero-badge-dot {
            width: 6px; height: 6px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        .auth-hero-logo {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 24px;
            color: white;
            margin-bottom: 28px;
            box-shadow: 0 0 40px rgba(16,185,129,0.3), 0 8px 24px rgba(0,0,0,0.4);
            letter-spacing: -0.5px;
        }

        .auth-hero h1 {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.1;
            color: white;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }
        .auth-hero h1 span {
            background: linear-gradient(90deg, #10b981, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-hero-desc {
            font-size: 16px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 48px;
        }

        /* Stats row */
        .auth-hero-stats {
            display: flex;
            gap: 32px;
        }
        .auth-hero-stat {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .auth-hero-stat-num {
            font-size: 28px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }
        .auth-hero-stat-num span {
            color: #10b981;
        }
        .auth-hero-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* Features list */
        .auth-hero-features {
            position: relative;
            z-index: 1;
        }
        .auth-hero-features-title {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 16px;
        }
        .auth-hero-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 13px;
            color: rgba(255,255,255,0.6);
        }
        .auth-hero-feature:last-child { border-bottom: none; }
        .auth-hero-feature-icon {
            width: 28px; height: 28px;
            background: rgba(16,185,129,0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #10b981;
        }

        /* Football pitch decorative lines */
        .auth-hero-pitch {
            position: absolute;
            bottom: 0; right: 0;
            width: 280px; height: 280px;
            opacity: 0.04;
        }

        /* ── Right Form Panel ──────────────────────────────────── */
        .auth-panel {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            background: #070f1c;
            position: relative;
        }
        @media (min-width: 960px) {
            .auth-panel {
                width: 480px;
                flex-shrink: 0;
                border-left: 1px solid rgba(255,255,255,0.05);
                padding: 48px 40px;
            }
        }

        .auth-panel-inner {
            width: 100%;
            max-width: 400px;
        }

        /* Mobile-only logo */
        .auth-mobile-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }
        @media (min-width: 960px) { .auth-mobile-logo { display: none; } }

        .auth-mobile-logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: white;
        }
        .auth-mobile-logo-text {
            font-size: 18px;
            font-weight: 700;
            color: white;
        }
        .auth-mobile-logo-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
        }

        /* Card heading */
        .auth-heading {
            margin-bottom: 32px;
        }
        .auth-heading-title {
            font-size: 26px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .auth-heading-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.45);
        }

        /* Form elements — use global CSS vars from app.css */
        .auth-form-group {
            margin-bottom: 18px;
        }
        .auth-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            margin-bottom: 7px;
        }
        .auth-label .req { color: #f59e0b; margin-left: 2px; }

        .auth-input-wrap {
            position: relative;
        }
        .auth-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.25);
            pointer-events: none;
        }
        .auth-input-icon-right {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.25);
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .auth-input-icon-right:hover { color: rgba(255,255,255,0.6); }

        .auth-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 11px 14px 11px 42px;
            font-size: 14px;
            color: white;
            font-family: inherit;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .auth-input.no-icon { padding-left: 14px; }
        .auth-input.has-right-icon { padding-right: 42px; }
        .auth-input::placeholder { color: rgba(255,255,255,0.2); }
        .auth-input:focus {
            border-color: rgba(16,185,129,0.5);
            background: rgba(16,185,129,0.05);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
        }
        .auth-input:focus + .auth-input-icon { color: #10b981; }
        .auth-input.error {
            border-color: rgba(239,68,68,0.5);
            background: rgba(239,68,68,0.05);
        }

        select.auth-input {
            appearance: none;
            cursor: pointer;
        }

        /* Two-column grid */
        .auth-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Checkbox row */
        .auth-checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .auth-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
        }
        .auth-checkbox-label input[type="checkbox"] {
            accent-color: #10b981;
            width: 15px; height: 15px;
        }
        .auth-forgot-link {
            font-size: 13px;
            color: #34d399;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .auth-forgot-link:hover { color: #10b981; }

        /* Submit button */
        .auth-btn {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 4px 20px rgba(16,185,129,0.25);
            letter-spacing: 0.2px;
        }
        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(16,185,129,0.35);
        }
        .auth-btn:active { transform: translateY(0); }

        /* Auth footer link */
        .auth-footer-link {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
        }
        .auth-footer-link a {
            color: #34d399;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .auth-footer-link a:hover { color: #10b981; }

        /* Alerts */
        .auth-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .auth-alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5;
        }
        .auth-alert-success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.25);
            color: #6ee7b7;
        }
        .auth-alert-info {
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.25);
            color: #93c5fd;
        }
        .auth-alert svg { flex-shrink: 0; margin-top: 1px; }

        /* Divider */
        .auth-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.07);
            margin: 24px 0;
        }

        /* Note box */
        .auth-note {
            background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: rgba(245,158,11,0.8);
            line-height: 1.5;
            margin-top: 16px;
        }

        /* Password strength */
        .pw-strength {
            margin-top: 6px;
        }
        .pw-strength-bars {
            display: flex;
            gap: 4px;
            margin-bottom: 4px;
        }
        .pw-strength-bar {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: rgba(255,255,255,0.1);
            transition: background 0.3s;
        }
        .pw-strength-bar.active-weak   { background: #ef4444; }
        .pw-strength-bar.active-fair   { background: #f59e0b; }
        .pw-strength-bar.active-good   { background: #3b82f6; }
        .pw-strength-bar.active-strong { background: #10b981; }
        .pw-strength-label {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
        }

        /* Role badge on form */
        .auth-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.25);
            border-radius: 20px;
            font-size: 12px;
            color: #34d399;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="auth-split">

    {{-- ═══ Left Hero Panel ══════════════════════════════════════════════════ --}}
    <div class="auth-hero">
        <div class="auth-hero-orb auth-hero-orb-1"></div>
        <div class="auth-hero-orb auth-hero-orb-2"></div>
        <div class="auth-hero-orb auth-hero-orb-3"></div>

        {{-- Pitch decoration --}}
        <svg class="auth-hero-pitch" viewBox="0 0 280 280" fill="none">
            <rect x="10" y="10" width="260" height="260" rx="4" stroke="white" stroke-width="2"/>
            <circle cx="140" cy="140" r="40" stroke="white" stroke-width="2"/>
            <line x1="140" y1="10" x2="140" y2="270" stroke="white" stroke-width="2"/>
            <rect x="10" y="90" width="50" height="100" stroke="white" stroke-width="2"/>
            <rect x="220" y="90" width="50" height="100" stroke="white" stroke-width="2"/>
            <circle cx="140" cy="140" r="2" fill="white"/>
        </svg>

        <div class="auth-hero-content">
            <div>
                <div class="auth-hero-badge">
                    <div class="auth-hero-badge-dot"></div>
                    SEASON 2025/26
                </div>

                <div class="auth-hero-logo">BFC</div>

                <h1>Beijing FC<br><span>Management</span></h1>

                <p class="auth-hero-desc">
                    Your all-in-one platform for match scheduling, financial tracking, league standings, and member management.
                </p>

                <div class="auth-hero-stats">
                    <div class="auth-hero-stat">
                        <div class="auth-hero-stat-num">KES <span>2,080</span></div>
                        <div class="auth-hero-stat-label">Monthly Fee</div>
                    </div>
                    <div class="auth-hero-stat">
                        <div class="auth-hero-stat-num"><span>4</span></div>
                        <div class="auth-hero-stat-label">Roles</div>
                    </div>
                    <div class="auth-hero-stat">
                        <div class="auth-hero-stat-num"><span>∞</span></div>
                        <div class="auth-hero-stat-label">Matches</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-hero-features">
            <div class="auth-hero-features-title">Platform Features</div>
            <div class="auth-hero-feature">
                <div class="auth-hero-feature-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                Match Scheduling & Team Generation
            </div>
            <div class="auth-hero-feature">
                <div class="auth-hero-feature-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
                M-Pesa Payments & Reconciliation
            </div>
            <div class="auth-hero-feature">
                <div class="auth-hero-feature-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                League Standings & History
            </div>
            <div class="auth-hero-feature">
                <div class="auth-hero-feature-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                Role-Based Member Management
            </div>
        </div>
    </div>

    {{-- ═══ Right Form Panel ══════════════════════════════════════════════════ --}}
    <div class="auth-panel">
        <div class="auth-panel-inner">

            {{-- Mobile logo --}}
            <div class="auth-mobile-logo">
                <div class="auth-mobile-logo-icon">BFC</div>
                <div>
                    <div class="auth-mobile-logo-text">Beijing FC</div>
                    <div class="auth-mobile-logo-sub">Management System</div>
                </div>
            </div>

            <div class="auth-heading">
                <div class="auth-heading-title">@yield('title', 'Sign In')</div>
                <div class="auth-heading-sub">@yield('subtitle', 'Welcome back to Beijing FC')</div>
            </div>

            @yield('content')

        </div>
    </div>

</div>

<script>
// Password show/hide toggle
function togglePassword(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    if (!input || !btn) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.querySelector('.eye-open').style.display  = isHidden ? 'none' : 'block';
    btn.querySelector('.eye-closed').style.display = isHidden ? 'block' : 'none';
}

// Password strength meter
function measureStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    return score; // 0-5
}

function updateStrength(inputId, barId, labelId) {
    const input  = document.getElementById(inputId);
    const bars   = document.querySelectorAll('#' + barId + ' .pw-strength-bar');
    const label  = document.getElementById(labelId);
    if (!input || !bars.length) return;

    input.addEventListener('input', () => {
        const score = measureStrength(input.value);
        const levels = ['', 'weak', 'fair', 'good', 'strong', 'strong'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        bars.forEach((bar, i) => {
            bar.className = 'pw-strength-bar';
            if (i < score) bar.classList.add('active-' + (levels[score] || ''));
        });
        if (label) label.textContent = input.value ? labels[score] || '' : '';
    });
}
</script>
</body>
</html>
