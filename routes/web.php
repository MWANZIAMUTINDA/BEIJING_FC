<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', function () {
    // 1. Next Match
    $dbNextMatch = \App\Models\FootballMatch::upcoming()->first();
    $nextMatch = $dbNextMatch ? [
        'id' => $dbNextMatch->id,
        'opponent' => $dbNextMatch->opponent ?? 'Opponent',
        'date' => $dbNextMatch->match_date,
        'time' => $dbNextMatch->match_time,
        'venue' => $dbNextMatch->venue,
        'fee' => $dbNextMatch->match_fee,
        'deadline' => $dbNextMatch->deadline,
        'is_mock' => false,
    ] : [
        'id' => 0,
        'opponent' => 'Kibera Black Stars',
        'date' => now()->addDays(3)->setHour(15)->setMinute(0)->setSecond(0),
        'time' => '15:00',
        'venue' => 'Camp Toyoyo Stadium, Nairobi',
        'fee' => 200,
        'deadline' => now()->addDays(2),
        'is_mock' => true,
    ];

    // 2. Recent Results
    $dbRecent = \App\Models\FootballMatch::where('status', 'completed')
        ->latest('match_date')->take(3)->get();
    
    $recentResults = [];
    if ($dbRecent->count() > 0) {
        foreach ($dbRecent as $m) {
            $recentResults[] = [
                'home_team' => 'Beijing FC',
                'away_team' => $m->opponent,
                'home_score' => $m->home_score ?? 0,
                'away_score' => $m->away_score ?? 0,
                'result' => ($m->home_score > $m->away_score) ? 'Win' : (($m->home_score < $m->away_score) ? 'Loss' : 'Draw'),
            ];
        }
    } else {
        $recentResults = [
            ['home_team' => 'Beijing FC', 'away_team' => 'South B FC', 'home_score' => 3, 'away_score' => 1, 'result' => 'Win'],
            ['home_team' => 'Eastlands FC', 'away_team' => 'Beijing FC', 'home_score' => 2, 'away_score' => 2, 'result' => 'Draw'],
            ['home_team' => 'Beijing FC', 'away_team' => 'Westlands United', 'home_score' => 1, 'away_score' => 0, 'result' => 'Win'],
        ];
    }

    // 3. Standings
    $dbStandings = \App\Models\Standing::with('team')
        ->where('season', '2025/2026')
        ->orderByDesc('points')
        ->orderByDesc('goal_difference')
        ->take(5)->get();
    
    $standings = [];
    if ($dbStandings->count() > 0) {
        foreach ($dbStandings as $i => $s) {
            $standings[] = [
                'rank' => $i + 1,
                'team' => $s->team?->name ?? 'Team',
                'short' => $s->team?->short_name ?? 'TEAM',
                'played' => $s->played,
                'wins' => $s->wins,
                'draws' => $s->draws,
                'losses' => $s->losses,
                'points' => $s->points,
                'gd' => $s->goal_difference,
            ];
        }
    } else {
        $standings = [
            ['rank' => 1, 'team' => 'Mathare United', 'short' => 'MUFC', 'played' => 12, 'wins' => 8, 'draws' => 0, 'losses' => 4, 'points' => 24, 'gd' => 10],
            ['rank' => 2, 'team' => 'Beijing FC', 'short' => 'BFC', 'played' => 11, 'wins' => 7, 'draws' => 1, 'losses' => 3, 'points' => 22, 'gd' => 14],
            ['rank' => 3, 'team' => 'Kibera Black Stars', 'short' => 'KBS', 'played' => 12, 'wins' => 6, 'draws' => 1, 'losses' => 5, 'points' => 19, 'gd' => 3],
            ['rank' => 4, 'team' => 'Kariobangi Sharks B', 'short' => 'KSB', 'played' => 12, 'wins' => 5, 'draws' => 3, 'losses' => 4, 'points' => 18, 'gd' => 1],
            ['rank' => 5, 'team' => 'South B FC', 'short' => 'SBFC', 'played' => 12, 'wins' => 4, 'draws' => 3, 'losses' => 5, 'points' => 15, 'gd' => -2],
        ];
    }

    // 4. Latest News
    $dbNews = \App\Models\Announcement::latest()->take(3)->get();
    $news = [];
    if ($dbNews->count() > 0) {
        foreach ($dbNews as $n) {
            $news[] = [
                'title' => $n->title,
                'summary' => Str::limit($n->body, 120),
                'date' => $n->created_at->format('d M Y'),
            ];
        }
    } else {
        $news = [
            [
                'title' => 'New Kit Sponsorship Unveiled for Season 2025/2026',
                'summary' => 'Beijing FC is thrilled to announce a new partnership with local sponsors, introducing our premium deep navy and emerald kit.',
                'date' => '02 Jul 2026',
            ],
            [
                'title' => 'Coach Outlines Tactical Training Plan for Next Match',
                'summary' => 'With the derby ahead, Coach is conducting intense defensive drills and set-piece strategies to secure a top-table spot.',
                'date' => '28 Jun 2026',
            ],
            [
                'title' => 'Beijing FC Welcomes Four New Signings to the Squad',
                'summary' => 'Strengthening our midfield and forward lines, the club has finalized contracts with four top local talents this week.',
                'date' => '15 Jun 2026',
            ],
        ];
    }

    // 5. Featured Players
    $dbPlayers = \App\Models\User::where('role', 'member')
        ->whereNotNull('position')
        ->take(4)->get();
    
    $players = [];
    if ($dbPlayers->count() > 0) {
        foreach ($dbPlayers as $p) {
            $players[] = [
                'name' => $p->name,
                'position' => $p->positionLabel(),
                'avatar' => $p->avatar_url,
                'stats' => $p->position === 'GK' ? '5 Clean Sheets' : ($p->position === 'FW' ? '8 Goals, 4 Assists' : ($p->position === 'MF' ? '2 Goals, 7 Assists' : '15 Interceptions')),
            ];
        }
    } else {
        $players = [
            ['name' => 'John Doe', 'position' => 'Goalkeeper', 'avatar' => null, 'stats' => '5 Clean Sheets'],
            ['name' => 'Jane Smith', 'position' => 'Forward', 'avatar' => null, 'stats' => '12 Goals, 4 Assists'],
            ['name' => 'Alex Mwangi', 'position' => 'Midfielder', 'avatar' => null, 'stats' => '3 Goals, 8 Assists'],
            ['name' => 'David Omwamba', 'position' => 'Defender', 'avatar' => null, 'stats' => '18 Clean Tackles'],
        ];
    }

    // 6. Club Stats
    $totalMembers = \App\Models\User::count();
    $totalPlayers = \App\Models\User::where('role', 'member')->count();
    
    $stats = [
        'players' => $totalPlayers > 0 ? $totalPlayers : 28,
        'members' => $totalMembers > 0 ? $totalMembers : 120,
        'wins' => 14,
        'goals' => 42,
    ];

    return view('welcome', compact('nextMatch', 'recentResults', 'standings', 'news', 'players', 'stats'));
})->name('home');

// ─── M-Pesa Webhook (no CSRF) ─────────────────────────────────────────────────
Route::post('/mpesa/webhook', [PaymentController::class, 'mpesaWebhook'])
    ->name('mpesa.webhook')
    ->withoutMiddleware(['web']);

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard (all roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (all roles)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Matches (Admin, Coach, Member) ──────────────────────────────────────
    Route::middleware(['role:admin,coach,member'])->group(function () {
        Route::resource('matches', MatchController::class)->except(['edit', 'update']);
        Route::post('matches/{match}/availability', [MatchController::class, 'updateAvailability'])->name('matches.availability');
        Route::post('matches/{match}/lock',         [MatchController::class, 'lockAvailability'])->name('matches.lock');
        Route::post('matches/{match}/teams',        [MatchController::class, 'generateTeams'])->name('matches.teams');
        Route::post('matches/{match}/result',       [MatchController::class, 'recordResult'])->name('matches.result');
    });

    // ─── Payments (Admin, Treasurer, Member) ─────────────────────────────────
    Route::middleware(['role:admin,treasurer,member'])->group(function () {
        Route::get('payments',                          [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/create',                   [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments',                         [PaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/reconcile',               [PaymentController::class, 'reconcile'])->name('payments.reconcile');
        Route::get('payments/export',                   [PaymentController::class, 'exportCsv'])->name('payments.export');
        Route::get('payments/{payment}/receipt',        [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('payments/statement',                [PaymentController::class, 'statement'])->name('payments.statement');
        Route::get('payments/statement/{user}',         [PaymentController::class, 'statement'])->name('payments.statement.user');
    });

    // ─── Treasurer Report (Admin, Treasurer only) ─────────────────────────────
    Route::middleware(['role:admin,treasurer'])->group(function () {
        Route::get('financial/treasurer-report', [PaymentController::class, 'treasurerReport'])->name('payments.treasurer_report');
    });

    // ─── League (Admin, Coach, Member) ───────────────────────────────────────
    Route::middleware(['role:admin,coach,member'])->group(function () {
        Route::get('league',         [LeagueController::class, 'standings'])->name('league.standings');
        Route::get('league/history', [LeagueController::class, 'history'])->name('league.history');
        Route::post('league/result', [LeagueController::class, 'recordResult'])->name('league.result');
    });

    // ─── Expenses (Admin, Treasurer) ─────────────────────────────────────────
    Route::middleware(['role:admin,treasurer'])->group(function () {
        Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    });

    // ─── Announcements (all roles) ────────────────────────────────────────────
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('announcements/{announcement}/read', [AnnouncementController::class, 'markRead'])->name('announcements.read');

    // ─── Admin-only ───────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });
});

require __DIR__ . '/auth.php';
