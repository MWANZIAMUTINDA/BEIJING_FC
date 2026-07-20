<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Standing;
use App\Models\Announcement;
use App\Models\User;
use App\Models\MemberBalance;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }
        return $this->memberDashboard($user);
    }

    private function adminDashboard()
    {
        $totalContributions = Payment::where('status', 'confirmed')->sum('amount');
        $totalExpenses      = Expense::where('is_approved', true)->sum('amount');
        $pendingPayments    = Payment::where('status', 'pending')->count();
        $totalMembers       = User::where('role', 'member')->where('is_active', true)->count();
        $outstandingBalance = MemberBalance::where('balance', '<', 0)->sum('balance');

        // Attendance rate: % of members who marked 'available' across all completed matches
        $completedMatches = FootballMatch::where('status', 'completed')->count();
        $attendanceRate   = 0;
        if ($completedMatches > 0 && $totalMembers > 0) {
            $totalAvailable   = Availability::whereHas('match', fn($q) => $q->where('status', 'completed'))
                ->where('status', 'available')
                ->count();
            $attendanceRate   = round(($totalAvailable / ($completedMatches * max(1, $totalMembers))) * 100);
            $attendanceRate   = min(100, $attendanceRate);
        }

        $upcomingMatches = FootballMatch::upcoming()
            ->with(['availabilities'])
            ->take(5)->get();

        $recentPayments = Payment::with('user')
            ->latest()->take(10)->get();

        $standings = Standing::with('team')
            ->where('season', '2025/2026')
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->get();

        $recentAnnouncements = Announcement::with('creator')
            ->latest()->take(5)->get();

        $membersWithDebt = MemberBalance::with('user')
            ->where('balance', '<', 0)
            ->orderBy('balance')
            ->take(8)->get();

        $expensesByCategory = Expense::where('is_approved', true)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')->get();

        // DB-driver-aware month grouping (MySQL vs SQLite)
        $isSQLite = \DB::connection()->getDriverName() === 'sqlite';
        $monthFmt  = $isSQLite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $monthFmtE = $isSQLite ? "strftime('%Y-%m', expense_date)" : "DATE_FORMAT(expense_date, '%Y-%m')";

        // Monthly income chart data (last 6 months)
        $monthlyIncome = Payment::where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("{$monthFmt} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Monthly expenses chart data (last 6 months)
        $monthlyExpenses = Expense::where('is_approved', true)
            ->where('expense_date', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("{$monthFmtE} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Monthly attendance chart data (last 6 months)
        $monthlyAttendance = Availability::whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->where('status', 'available')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("{$monthFmt} as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build 6-month label array
        $months      = collect();
        $incomeData  = collect();
        $expenseData = collect();
        $attendData  = collect();
        for ($i = 5; $i >= 0; $i--) {
            $dt     = now()->subMonths($i);
            $key    = $dt->format('Y-m');
            $label  = $dt->format('M Y');
            $months->push($label);
            $incomeData->push($monthlyIncome->get($key)?->total ?? 0);
            $expenseData->push($monthlyExpenses->get($key)?->total ?? 0);
            $attendData->push($monthlyAttendance->get($key)?->total ?? 0);
        }

        // League performance (BFC team wins/draws/losses from standings)
        $bfcStanding = $standings->first(); // assume first is BFC or use team name filter
        $leaguePerformance = [
            'labels' => ['Wins', 'Draws', 'Losses'],
            'data'   => [
                $bfcStanding?->wins   ?? 0,
                $bfcStanding?->draws  ?? 0,
                $bfcStanding?->losses ?? 0,
            ],
        ];

        // Compact chart JSON for blade
        $chartMonths      = $months->toJson();
        $chartIncome      = $incomeData->toJson();
        $chartExpenses    = $expenseData->toJson();
        $chartAttendance  = $attendData->toJson();
        $chartLeague      = json_encode($leaguePerformance);

        // Legacy monthlyData kept for backward compat
        $monthlyData = $monthlyIncome;

        return view('dashboard.admin', compact(
            'totalContributions', 'totalExpenses', 'pendingPayments',
            'totalMembers', 'outstandingBalance', 'upcomingMatches',
            'recentPayments', 'standings', 'recentAnnouncements',
            'membersWithDebt', 'expensesByCategory', 'monthlyData',
            'attendanceRate', 'completedMatches',
            'chartMonths', 'chartIncome', 'chartExpenses',
            'chartAttendance', 'chartLeague'
        ));
    }

    private function memberDashboard(User $user)
    {
        $balance = $user->balance ?? new MemberBalance(['balance' => 0, 'total_paid' => 0, 'total_owed' => 0]);

        $nextMatch = FootballMatch::upcoming()->first();
        $myAvailability = $nextMatch
            ? $user->availabilityFor($nextMatch->id)
            : null;

        $recentPayments = $user->payments()
            ->with('match')->latest()->take(6)->get();

        $upcomingMatches = FootballMatch::upcoming()
            ->take(4)->get();

        $standings = Standing::with('team')
            ->where('season', '2025/2026')
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->get();

        $announcements = Announcement::latest()->take(5)->get();

        $matchesPlayed = $user->availabilities()
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->where('status', 'available')->count();

        // Member attendance rate (% of completed matches they attended)
        $totalCompleted = FootballMatch::where('status', 'completed')->count();
        $memberAttendanceRate = $totalCompleted > 0
            ? round(($matchesPlayed / $totalCompleted) * 100)
            : 0;

        // Member monthly payment history for chart (last 6 months)
        $isSQLite2 = \DB::connection()->getDriverName() === 'sqlite';
        $mFmt = $isSQLite2 ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $memberPayHistory = $user->payments()
            ->where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("{$mFmt} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartMonths      = collect();
        $chartMemberPay   = collect();
        for ($i = 5; $i >= 0; $i--) {
            $dt    = now()->subMonths($i);
            $key   = $dt->format('Y-m');
            $chartMonths->push($dt->format('M Y'));
            $chartMemberPay->push($memberPayHistory->get($key)?->total ?? 0);
        }

        $memberChartMonths = $chartMonths->toJson();
        $memberChartPay    = $chartMemberPay->toJson();

        return view('dashboard.member', compact(
            'user', 'balance', 'nextMatch', 'myAvailability',
            'recentPayments', 'upcomingMatches', 'standings',
            'announcements', 'matchesPlayed',
            'memberAttendanceRate', 'totalCompleted',
            'memberChartMonths', 'memberChartPay'
        ));
    }
}
