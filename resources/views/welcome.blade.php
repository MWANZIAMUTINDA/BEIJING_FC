<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beijing FC — One Team • One Dream</title>
    <meta name="description" content="Official website of Beijing FC. Follow match updates, player stats, league standings, and join the club membership.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* ─── Design Tokens ─────────────────────────────────────────────────────────── */
        :root {
            --navy-950: #050d1a;
            --navy-900: #0B1F4D; /* Theme main dark blue */
            --navy-850: #0e2861;
            --navy-800: #123175;
            --navy-700: #184099;
            --emerald-500: #10B981;
            --emerald-400: #34D399;
            --gold-500: #FBBF24;
            --gold-400: #FCD34D;
            --text-primary: #F1F5F9;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
            --bg-white: #FFFFFF;
            --bg-light: #F8FAFC;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 30px rgba(11,31,77,0.08);
            --shadow-lg: 0 12px 40px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── Reset & Base ───────────────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-white);
            color: #1e293b;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ─── Typography ──────────────────────────────────────────────────────────────── */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: var(--navy-900);
        }

        /* ─── Header & Navigation ─────────────────────────────────────────────────────── */
        header {
            position: fixed;
            top: 0; left: 0; width: 100%;
            height: 80px;
            background: transparent;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5%;
            z-index: 1000;
            transition: var(--transition);
        }
        header.scrolled {
            background: rgba(11, 31, 77, 0.95);
            backdrop-filter: blur(12px);
            height: 70px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .logo-wrap {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none;
        }
        .logo-badge {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--emerald-500), #059669);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 900; font-size: 20px; color: white;
            box-shadow: 0 0 15px rgba(16,185,129,0.3);
        }
        .logo-text { display: flex; flex-direction: column; }
        .logo-title { font-weight: 800; font-size: 18px; color: white; line-height: 1.1; }
        .logo-sub { font-size: 10px; color: var(--emerald-400); font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; }

        .nav-menu {
            display: flex; align-items: center; gap: 32px;
            list-style: none;
        }
        .nav-link {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px; font-weight: 500;
            transition: var(--transition);
            position: relative;
        }
        .nav-link:hover { color: white; }
        .nav-link::after {
            content: '';
            position: absolute; bottom: -6px; left: 0;
            width: 0; height: 2px;
            background: var(--emerald-400);
            transition: var(--transition);
        }
        .nav-link:hover::after { width: 100%; }

        .header-actions { display: flex; align-items: center; gap: 16px; }

        /* Hamburguer menu for mobile */
        .mobile-toggle {
            display: none;
            background: none; border: none;
            color: white; font-size: 28px; cursor: pointer;
        }

        /* ─── Buttons ─────────────────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            font-size: 14px; font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--emerald-500), #059669);
            color: white;
            box-shadow: 0 4px 14px rgba(16,185,129,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--gold-500), #D97706);
            color: white;
            box-shadow: 0 4px 14px rgba(245,158,11,0.3);
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245,158,11,0.4);
        }

        /* ─── Hero Section ────────────────────────────────────────────────────────────── */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 680px;
            background: url("{{ asset('images/stadium_bg.png') }}") center/cover no-repeat;
            display: flex; align-items: center;
            padding: 0 5%;
            color: white;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(5, 13, 26, 0.95) 0%, rgba(5, 13, 26, 0.7) 40%, rgba(5, 13, 26, 0.3) 100%);
            z-index: 1;
        }
        .hero-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .hero-tag {
            font-size: 12px; font-weight: 700; color: var(--emerald-400);
            letter-spacing: 2px; text-transform: uppercase;
            margin-bottom: 12px; display: inline-block;
        }
        .hero h1 {
            font-size: clamp(36px, 5vw, 64px);
            line-height: 1.1; color: white;
            margin-bottom: 16px;
        }
        .hero h1 span { color: var(--gold-400); }
        .hero-desc {
            font-size: clamp(14px, 1.8vw, 18px);
            color: var(--text-secondary);
            margin-bottom: 32px; max-width: 580px;
        }
        .hero-btns { display: flex; gap: 16px; }

        /* Hero Next Match Widget */
        .hero-match-widget {
            background: rgba(11, 31, 77, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-lg);
        }
        .widget-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            color: var(--emerald-400); letter-spacing: 1.5px;
            margin-bottom: 16px;
        }
        .widget-matchup {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .team-box { text-align: center; flex: 1; }
        .team-crest {
            font-size: 32px; margin-bottom: 8px;
        }
        .team-name { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 14px; color: white; }
        .vs-circle {
            width: 32px; height: 32px;
            border-radius: 50%; background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: var(--text-secondary);
            margin: 0 12px;
        }
        .countdown-row {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 8px; text-align: center;
        }
        .cd-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-sm);
            padding: 8px 4px;
        }
        .cd-val { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--gold-400); }
        .cd-unit { font-size: 9px; text-transform: uppercase; color: var(--text-muted); }

        /* ─── Section Common ──────────────────────────────────────────────────────────── */
        section {
            padding: 90px 5%;
            position: relative;
        }
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-tag {
            font-size: 11px; font-weight: 700; color: var(--emerald-500);
            letter-spacing: 2px; text-transform: uppercase;
            margin-bottom: 8px; display: inline-block;
        }
        .section-title {
            font-size: 36px; font-weight: 800;
        }
        .section-desc {
            color: var(--text-muted);
            font-size: 15px; margin-top: 10px;
            max-width: 600px; margin-left: auto; margin-right: auto;
        }

        /* ─── Club Statistics Section ─────────────────────────────────────────────────── */
        .stats-sec {
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-950) 100%);
            color: white;
            padding: 60px 5%;
        }
        .stats-grid {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            position: relative;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-card-val {
            font-family: 'Outfit', sans-serif;
            font-weight: 900; font-size: 48px;
            line-height: 1; color: var(--emerald-400);
            margin-bottom: 8px;
        }
        .stat-card:nth-child(even) .stat-card-val { color: var(--gold-400); }
        .stat-card-lbl {
            font-size: 13px; color: var(--text-secondary);
            font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
        }

        /* ─── Latest News Section ─────────────────────────────────────────────────────── */
        .news-grid {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        .news-card {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0,0,0,0.05);
            display: flex; flex-direction: column;
            transition: var(--transition);
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(11,31,77,0.15);
        }
        .news-img {
            height: 200px;
            background: linear-gradient(135deg, var(--navy-850) 0%, var(--navy-900) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: rgba(255,255,255,0.1);
            position: relative;
        }
        .news-img::after {
            content: '⚽';
            font-size: 64px; opacity: 0.15;
        }
        .news-content {
            padding: 24px; flex: 1;
            display: flex; flex-direction: column;
            justify-content: space-between;
        }
        .news-date {
            font-size: 11px; font-weight: 600; color: var(--emerald-500);
            margin-bottom: 8px;
        }
        .news-title {
            font-size: 18px; font-weight: 700; line-height: 1.3;
            margin-bottom: 12px; color: var(--navy-900);
        }
        .news-text {
            font-size: 13px; color: var(--text-muted);
            margin-bottom: 20px; line-height: 1.5;
        }
        .news-btn {
            font-size: 13px; font-weight: 700; color: var(--navy-900);
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        }
        .news-btn:hover { color: var(--emerald-500); }

        /* ─── Next Match Detailed Section ──────────────────────────────────────────────── */
        .match-sec {
            background: var(--bg-light);
        }
        .match-banner {
            max-width: 1000px; margin: 0 auto;
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-950) 100%);
            border-radius: var(--radius-lg);
            padding: 40px; color: white;
            box-shadow: var(--shadow-lg);
            text-align: center;
            position: relative; overflow: hidden;
        }
        .match-banner::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at top right, rgba(16,185,129,0.1) 0%, transparent 60%);
        }
        .match-banner-title {
            font-size: 12px; font-weight: 700; color: var(--gold-400);
            letter-spacing: 2px; text-transform: uppercase; margin-bottom: 30px;
        }
        .scoreboard {
            display: flex; align-items: center; justify-content: center;
            gap: 40px; margin-bottom: 30px;
        }
        .sb-team { text-align: center; width: 220px; }
        .sb-crest { font-size: 54px; margin-bottom: 12px; }
        .sb-name { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 24px; }
        .sb-vs {
            font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 900;
            color: var(--text-secondary);
            background: rgba(255,255,255,0.05);
            width: 64px; height: 64px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .match-meta-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            max-width: 600px; margin: 0 auto 40px;
            background: rgba(255,255,255,0.05);
            border-radius: var(--radius-sm);
            padding: 16px 0;
        }
        .meta-cell { border-right: 1px solid rgba(255,255,255,0.1); }
        .meta-cell:last-child { border-right: none; }
        .meta-label { font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; }
        .meta-val { font-size: 14px; font-weight: 600; margin-top: 4px; }
        
        .timer-row {
            display: flex; justify-content: center; gap: 16px; margin-bottom: 40px;
        }
        .t-box {
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-sm);
            width: 70px; padding: 12px 0;
        }
        .t-val { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 900; color: var(--emerald-400); }
        .t-unit { font-size: 10px; color: var(--text-secondary); text-transform: uppercase; }

        /* ─── League Standings Section ─────────────────────────────────────────────────── */
        .league-sec { background: var(--bg-white); }
        .league-container {
            max-width: 1000px; margin: 0 auto;
        }
        .standings-card {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .l-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .l-table th {
            background: var(--navy-900); color: white;
            padding: 14px 20px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .l-table td { padding: 14px 20px; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .l-table tr:last-child td { border-bottom: none; }
        .l-table tr:hover td { background: var(--bg-light); }
        .l-table tr.highlight td { background: rgba(16,185,129,0.05); }
        .td-rank { font-weight: 700; width: 40px; }
        .td-team { font-weight: 600; color: var(--navy-900); }
        .l-table tr.highlight .td-team { color: var(--emerald-500); }
        
        .league-footer {
            text-align: center; margin-top: 30px;
        }

        /* ─── Featured Players Section ─────────────────────────────────────────────────── */
        .players-sec { background: var(--bg-light); }
        .players-grid {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .player-card {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
            transition: var(--transition);
        }
        .player-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(11,31,77,0.12);
        }
        .player-img-placeholder {
            height: 220px;
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-950) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 72px; position: relative;
        }
        .player-img-placeholder::after {
            content: '👤';
            opacity: 0.12;
        }
        .player-badge-overlay {
            position: absolute; bottom: 12px; right: 12px;
            background: rgba(16,185,129,0.2);
            border: 1px solid var(--emerald-400);
            color: var(--emerald-400);
            padding: 2px 8px; border-radius: 12px;
            font-size: 10px; font-weight: 700;
        }
        .player-info { padding: 20px; }
        .player-name { font-size: 16px; font-weight: 700; color: var(--navy-900); }
        .player-pos {
            font-size: 11px; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;
        }
        .player-stats {
            background: var(--bg-light);
            border-radius: var(--radius-sm);
            padding: 8px; margin-top: 12px;
            font-size: 12px; font-weight: 700; color: var(--emerald-500);
        }

        /* ─── Recent Match Results Section ──────────────────────────────────────────────── */
        .results-sec { background: var(--bg-white); }
        .results-list {
            max-width: 800px; margin: 0 auto;
            display: flex; flex-direction: column; gap: 16px;
        }
        .result-row {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: var(--shadow-sm);
            padding: 20px 30px;
            display: flex; align-items: center; justify-content: space-between;
            transition: var(--transition);
        }
        .result-row:hover { transform: translateX(4px); box-shadow: var(--shadow-md); }
        .result-teams { display: flex; align-items: center; gap: 20px; font-weight: 700; font-size: 15px; }
        .result-badge {
            padding: 4px 12px; border-radius: 12px;
            font-size: 10px; font-weight: 700; text-transform: uppercase;
        }
        .result-win  { background: rgba(16,185,129,0.15); color: var(--emerald-500); }
        .result-draw { background: rgba(245,158,11,0.15); color: #D97706; }
        .result-loss { background: rgba(239,68,68,0.15); color: #EF4444; }

        /* ─── Sponsors Section ────────────────────────────────────────────────────────── */
        .sponsors-sec {
            background: var(--bg-light);
            padding: 60px 5%;
            text-align: center;
        }
        .sponsors-grid {
            display: flex; justify-content: center; align-items: center;
            gap: 60px; flex-wrap: wrap;
            max-width: 1000px; margin: 0 auto;
        }
        .sponsor-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 800; font-size: 20px; color: var(--text-muted);
            opacity: 0.6; cursor: default;
            transition: var(--transition);
        }
        .sponsor-logo:hover { opacity: 1; color: var(--navy-900); }

        /* ─── Footer Section ──────────────────────────────────────────────────────────── */
        footer {
            background: var(--navy-950);
            color: white;
            padding: 60px 5% 30px;
            font-size: 14px;
        }
        .footer-grid {
            max-width: 1200px; margin: 0 auto 40px;
            display: grid; grid-template-columns: 1.5fr 1fr 1fr;
            gap: 40px;
        }
        .footer-logo-title { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 900; margin-bottom: 12px; }
        .footer-logo-sub { color: var(--emerald-400); font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 20px; }
        .footer-about { color: var(--text-secondary); max-width: 320px; margin-bottom: 24px; }
        .footer-title { font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 20px; color: var(--gold-400); }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { text-decoration: none; color: var(--text-secondary); transition: var(--transition); }
        .footer-links a:hover { color: white; }
        .footer-contact { display: flex; flex-direction: column; gap: 12px; color: var(--text-secondary); }
        .footer-contact-item { display: flex; align-items: flex-start; gap: 10px; }

        .footer-bottom {
            max-width: 1200px; margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 24px;
            display: flex; align-items: center; justify-content: space-between;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* ─── Responsive Styles ──────────────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .hero-container { grid-template-columns: 1fr; }
            .hero-match-widget { max-width: 440px; margin: 0 auto; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .news-grid { grid-template-columns: repeat(2, 1fr); }
            .players-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            header { padding: 0 24px; }
            .nav-menu { display: none; }
            .mobile-toggle { display: block; }
            .hero-btns { flex-direction: column; }
            .stats-grid { grid-template-columns: 1fr; }
            .news-grid { grid-template-columns: 1fr; }
            .players-grid { grid-template-columns: 1fr; }
            .scoreboard { flex-direction: column; gap: 20px; }
            .sb-team { width: auto; }
            .match-meta-grid { grid-template-columns: 1fr; gap: 12px; }
            .meta-cell { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px; }
            .meta-cell:last-child { border-bottom: none; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>

    {{-- ═══ Header Section ══════════════════════════════════════════════════ --}}
    <header id="mainHeader">
        <a href="{{ route('home') }}" class="logo-wrap">
            <div class="logo-badge">BFC</div>
            <div class="logo-text">
                <span class="logo-title">Beijing FC</span>
                <span class="logo-sub">Management</span>
            </div>
        </a>

        <ul class="nav-menu">
            <li><a href="#" class="nav-link">Home</a></li>
            <li><a href="#news" class="nav-link">News</a></li>
            <li><a href="#fixtures" class="nav-link">Fixtures</a></li>
            <li><a href="#players" class="nav-link">Players</a></li>
            <li><a href="#league" class="nav-link">League</a></li>
            <li><a href="#sponsors" class="nav-link">Sponsors</a></li>
        </ul>

        <div class="header-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Join Club</a>
            @endauth
            <button class="mobile-toggle" onclick="toggleMobileMenu()">☰</button>
        </div>
    </header>

    {{-- ═══ Hero Section ════════════════════════════════════════════════════ --}}
    <section class="hero">
        <div class="hero-container">
            <div>
                <span class="hero-tag">Official Club Portal</span>
                <h1>One Team<br><span>One Dream</span></h1>
                <p class="hero-desc">
                    Welcome to the digital home of Beijing FC. Experience premium match tracking, automated team line-ups, direct M-Pesa payments, and interactive league analytics.
                </p>
                <div class="hero-btns">
                    <a href="{{ route('register') }}" class="btn btn-primary">Join the Club</a>
                    <a href="#fixtures" class="btn btn-secondary">View Match Details</a>
                </div>
            </div>

            <div>
                <div class="hero-match-widget">
                    <div class="widget-title">Next Fixture</div>
                    <div class="widget-matchup">
                        <div class="team-box">
                            <div class="team-crest">🟢</div>
                            <div class="team-name">Beijing FC</div>
                        </div>
                        <div class="vs-circle">VS</div>
                        <div class="team-box">
                            <div class="team-crest">⚪</div>
                            <div class="team-name">{{ $nextMatch['opponent'] }}</div>
                        </div>
                    </div>
                    <div class="countdown-row" id="heroCountdown">
                        <div class="cd-box"><div class="cd-val" id="hd-days">00</div><div class="cd-unit">Days</div></div>
                        <div class="cd-box"><div class="cd-val" id="hd-hours">00</div><div class="cd-unit">Hrs</div></div>
                        <div class="cd-box"><div class="cd-val" id="hd-mins">00</div><div class="cd-unit">Mins</div></div>
                        <div class="cd-box"><div class="cd-val" id="hd-secs">00</div><div class="cd-unit">Secs</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ Club Statistics Section ════════════════════════════════════════ --}}
    <section class="stats-sec">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-val">{{ $stats['players'] }}</div>
                <div class="stat-card-lbl">Registered Players</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-val">{{ $stats['wins'] }}</div>
                <div class="stat-card-lbl">Matches Won</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-val">{{ $stats['goals'] }}</div>
                <div class="stat-card-lbl">Total Goals</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-val">{{ $stats['members'] }}</div>
                <div class="stat-card-lbl">Club Members</div>
            </div>
        </div>
    </section>

    {{-- ═══ Latest News Section ════════════════════════════════════════════ --}}
    <section id="news">
        <div class="section-header">
            <span class="section-tag">Updates</span>
            <h2 class="section-title">Latest Club News</h2>
            <p class="section-desc">Stay informed with the latest reports, announcements, and match previews directly from our coaching staff.</p>
        </div>
        
        <div class="news-grid">
            @foreach($news as $item)
            <div class="news-card">
                <div class="news-img"></div>
                <div class="news-content">
                    <div>
                        <div class="news-date">{{ $item['date'] }}</div>
                        <h3 class="news-title">{{ $item['title'] }}</h3>
                        <p class="news-text">{{ $item['summary'] }}</p>
                    </div>
                    <a href="#" class="news-btn">Read More →</a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ Next Match Section ═════════════════════════════════════════════ --}}
    <section id="fixtures" class="match-sec">
        <div class="section-header">
            <span class="section-tag">Matchday</span>
            <h2 class="section-title">Upcoming Fixture</h2>
            <p class="section-desc">Check out the upcoming clash, view fee requirements, and lock in your player availability.</p>
        </div>

        <div class="match-banner">
            <div class="match-banner-title">Upcoming Match Details</div>
            
            <div class="scoreboard">
                <div class="sb-team">
                    <div class="sb-crest">🟢</div>
                    <div class="sb-name">Beijing FC</div>
                </div>
                <div class="sb-vs">VS</div>
                <div class="sb-team">
                    <div class="sb-crest">⚪</div>
                    <div class="sb-name">{{ $nextMatch['opponent'] }}</div>
                </div>
            </div>

            <div class="match-meta-grid">
                <div class="meta-cell">
                    <div class="meta-label">Kickoff Time</div>
                    <div class="meta-val" style="color:var(--emerald-400);">{{ $nextMatch['time'] }}</div>
                </div>
                <div class="meta-cell">
                    <div class="meta-label">Venue</div>
                    <div class="meta-val">{{ Str::before($nextMatch['venue'], ',') }}</div>
                </div>
                <div class="meta-cell">
                    <div class="meta-label">Match Fee</div>
                    <div class="meta-val" style="color:var(--gold-400);">KSh {{ number_format($nextMatch['fee']) }}</div>
                </div>
            </div>

            <div class="timer-row" id="sectionCountdown">
                <div class="t-box"><div class="t-val" id="sd-days">00</div><div class="t-unit">Days</div></div>
                <div class="t-box"><div class="t-val" id="sd-hours">00</div><div class="t-unit">Hrs</div></div>
                <div class="t-box"><div class="t-val" id="sd-mins">00</div><div class="t-unit">Mins</div></div>
                <div class="t-box"><div class="t-val" id="sd-secs">00</div><div class="t-unit">Secs</div></div>
            </div>

            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-gold">Manage Availability</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary">Register to Participate</a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ═══ League Standings Section ═══════════════════════════════════════ --}}
    <section id="league" class="league-sec">
        <div class="section-header">
            <span class="section-tag">Performance</span>
            <h2 class="section-title">League Standings</h2>
            <p class="section-desc">Track our team's performance, wins, goals, and points in the ongoing regional campaign.</p>
        </div>

        <div class="league-container">
            <div class="standings-card">
                <table class="l-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Pos</th>
                            <th>Team</th>
                            <th style="text-align:center;">P</th>
                            <th style="text-align:center;">W</th>
                            <th style="text-align:center;">D</th>
                            <th style="text-align:center;">L</th>
                            <th style="text-align:center;">GD</th>
                            <th style="text-align:center;">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($standings as $s)
                        <tr class="{{ $s['team'] === 'Beijing FC' ? 'highlight' : '' }}">
                            <td style="text-align:center;" class="td-rank">{{ $s['rank'] }}</td>
                            <td class="td-team">{{ $s['team'] }}</td>
                            <td style="text-align:center;">{{ $s['played'] }}</td>
                            <td style="text-align:center;">{{ $s['wins'] }}</td>
                            <td style="text-align:center;">{{ $s['draws'] }}</td>
                            <td style="text-align:center;">{{ $s['losses'] }}</td>
                            <td style="text-align:center; color:{{ $s['gd'] >= 0 ? 'var(--emerald-500)' : '#EF4444' }};">
                                {{ $s['gd'] > 0 ? '+' : '' }}{{ $s['gd'] }}
                            </td>
                            <td style="text-align:center; font-weight:800; color:var(--navy-900);">{{ $s['points'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="league-footer">
                @auth
                    <a href="{{ route('league.standings') }}" class="btn btn-secondary">View Full Standings Table</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary">Sign In to View Full League Records</a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ═══ Featured Players Section ══════════════════════════════════════ --}}
    <section id="players" class="players-sec">
        <div class="section-header">
            <span class="section-tag">Squad</span>
            <h2 class="section-title">Featured Players</h2>
            <p class="section-desc">Meet the outstanding athletes making differences on the pitch this season.</p>
        </div>

        <div class="players-grid">
            @foreach($players as $p)
            <div class="player-card">
                <div class="player-img-placeholder">
                    <span class="player-badge-overlay">{{ $p['position'] }}</span>
                </div>
                <div class="player-info">
                    <div class="player-name">{{ $p['name'] }}</div>
                    <div class="player-pos">{{ $p['position'] }}</div>
                    <div class="player-stats">{{ $p['stats'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ Recent Match Results Section ═══════════════════════════════════ --}}
    <section class="results-sec">
        <div class="section-header">
            <span class="section-tag">Results</span>
            <h2 class="section-title">Recent Performances</h2>
            <p class="section-desc">A summary of our most recent scores and overall form indicators.</p>
        </div>

        <div class="results-list">
            @foreach($recentResults as $res)
            <div class="result-row">
                <div class="result-teams">
                    <span>{{ $res['home_team'] }}</span>
                    <span style="color:var(--text-muted);font-weight:400;">vs</span>
                    <span>{{ $res['away_team'] }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:20px;">
                    <div style="font-family:'Outfit',sans-serif;font-weight:800;font-size:18px;color:var(--navy-900);">
                        {{ $res['home_score'] }} - {{ $res['away_score'] }}
                    </div>
                    <span class="result-badge {{ $res['result'] === 'Win' ? 'result-win' : ($res['result'] === 'Draw' ? 'result-draw' : 'result-loss') }}">
                        {{ $res['result'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ Sponsors Section ══════════════════════════════════════════════ --}}
    <section id="sponsors" class="sponsors-sec">
        <div class="sponsors-grid">
            <div class="sponsor-logo">EMERALD GLOBAL</div>
            <div class="sponsor-logo">GOLD SHIELD PARTNERS</div>
            <div class="sponsor-logo">NATIVE BRANDING</div>
            <div class="sponsor-logo">AFRICA PAYMENTS</div>
        </div>
    </section>

    {{-- ═══ Footer Section ════════════════════════════════════════════════ --}}
    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-logo-title">Beijing FC</div>
                <div class="footer-logo-sub">One Team • One Dream</div>
                <p class="footer-about">
                    Transitioning community football operations from manual tracking to a secure, modern, and transparent digital management system.
                </p>
            </div>
            
            <div>
                <div class="footer-title">Quick Links</div>
                <ul class="footer-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#news">News</a></li>
                    <li><a href="#fixtures">Fixtures</a></li>
                    <li><a href="#players">Players</a></li>
                    <li><a href="#league">League Standings</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-title">Contact Us</div>
                <div class="footer-contact">
                    <div class="footer-contact-item">
                        <span>📍</span>
                        <span>Camp Toyoyo Stadium Ground, Nairobi, Kenya</span>
                    </div>
                    <div class="footer-contact-item">
                        <span>📞</span>
                        <span>+254 700 000 001</span>
                    </div>
                    <div class="footer-contact-item">
                        <span>✉</span>
                        <span>info@beijingfc.co.ke</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; {{ date('Y') }} Beijing FC Management. All Rights Reserved.</div>
            <div style="display:flex;gap:20px;">
                <a href="#" style="text-decoration:none;color:var(--text-muted);">Privacy Policy</a>
                <a href="#" style="text-decoration:none;color:var(--text-muted);">Terms of Service</a>
            </div>
        </div>
    </footer>

    {{-- ═══ JavaScript Logic ══════════════════════════════════════════════ --}}
    <script>
        // Sticky Header scroll interaction
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Countdown Timer Logic
        const targetDate = new Date("{{ $nextMatch['date']->toIso8601String() }}").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(interval);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const format = (n) => String(n).padStart(2, '0');

            // Hero Widget countdown
            const hdDays = document.getElementById('hd-days');
            const hdHours = document.getElementById('hd-hours');
            const hdMins = document.getElementById('hd-mins');
            const hdSecs = document.getElementById('hd-secs');
            
            if (hdDays) {
                hdDays.innerText = format(days);
                hdHours.innerText = format(hours);
                hdMins.innerText = format(minutes);
                hdSecs.innerText = format(seconds);
            }

            // Detailed Section countdown
            const sdDays = document.getElementById('sd-days');
            const sdHours = document.getElementById('sd-hours');
            const sdMins = document.getElementById('sd-mins');
            const sdSecs = document.getElementById('sd-secs');

            if (sdDays) {
                sdDays.innerText = format(days);
                sdHours.innerText = format(hours);
                sdMins.innerText = format(minutes);
                sdSecs.innerText = format(seconds);
            }
        }

        const interval = setInterval(updateCountdown, 1000);
        updateCountdown();

        // Mobile responsive toggle
        function toggleMobileMenu() {
            const menu = document.querySelector('.nav-menu');
            if (menu.style.display === 'flex') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'flex';
                menu.style.flexDirection = 'column';
                menu.style.position = 'absolute';
                menu.style.top = '70px';
                menu.style.left = '0';
                menu.style.width = '100%';
                menu.style.background = 'var(--navy-900)';
                menu.style.padding = '20px';
                menu.style.gap = '20px';
            }
        }
    </script>
</body>
</html>
