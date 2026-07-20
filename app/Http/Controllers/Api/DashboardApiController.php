<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Standing;
use App\Models\Announcement;
use App\Models\User;
use App\Models\MemberBalance;
use App\Models\Availability;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isTreasurer() || $user->isCoach()) {
            return $this->adminDashboardData();
        }

        return $this->memberDashboardData($user);
    }

    private function adminDashboardData()
    {
        $totalContributions = Payment::where('status', 'confirmed')->sum('amount');
        $totalExpenses      = Expense::where('is_approved', true)->sum('amount');
        $pendingPayments    = Payment::where('status', 'pending')->count();
        $totalMembers       = User::where('role', 'member')->where('is_active', true)->count();
        $outstandingBalance = MemberBalance::where('balance', '<', 0)->sum('balance');

        $completedMatches = FootballMatch::where('status', 'completed')->count();
        $attendanceRate   = 0;
        if ($completedMatches > 0 && $totalMembers > 0) {
            $totalAvailable = Availability::whereHas('match', fn($q) => $q->where('status', 'completed'))
                ->where('status', 'available')
                ->count();
            $attendanceRate = round(($totalAvailable / ($completedMatches * max(1, $totalMembers))) * 100);
            $attendanceRate = min(100, $attendanceRate);
        }

        $upcomingMatches = FootballMatch::upcoming()
            ->with(['availabilities'])
            ->take(5)->get();

        $recentPayments = Payment::with('user')
            ->latest()->take(10)->get();

        $recentAnnouncements = Announcement::with('creator')
            ->latest()->take(5)->get();

        return response()->json([
            'status' => 'success',
            'role' => auth()->user()->role,
            'data' => [
                'total_contributions' => (float)$totalContributions,
                'total_expenses' => (float)$totalExpenses,
                'net_balance' => (float)($totalContributions - $totalExpenses),
                'pending_payments_count' => $pendingPayments,
                'total_members_count' => $totalMembers,
                'outstanding_debt' => (float)abs($outstandingBalance),
                'attendance_rate' => $attendanceRate,
                'upcoming_matches' => $upcomingMatches,
                'recent_payments' => $recentPayments,
                'recent_announcements' => $recentAnnouncements,
            ]
        ]);
    }

    private function memberDashboardData(User $user)
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

        $announcements = Announcement::latest()->take(5)->get();

        $matchesPlayed = $user->availabilities()
            ->whereHas('match', fn($q) => $q->where('status', 'completed'))
            ->where('status', 'available')->count();

        $totalCompleted = FootballMatch::where('status', 'completed')->count();
        $memberAttendanceRate = $totalCompleted > 0
            ? round(($matchesPlayed / $totalCompleted) * 100)
            : 0;

        return response()->json([
            'status' => 'success',
            'role' => 'member',
            'data' => [
                'balance' => (float)$balance->balance,
                'total_paid' => (float)$balance->total_paid,
                'total_owed' => (float)$balance->total_owed,
                'next_match' => $nextMatch,
                'my_availability' => $myAvailability,
                'recent_payments' => $recentPayments,
                'upcoming_matches' => $upcomingMatches,
                'announcements' => $announcements,
                'matches_played_count' => $matchesPlayed,
                'attendance_rate' => $memberAttendanceRate,
            ]
        ]);
    }
}
