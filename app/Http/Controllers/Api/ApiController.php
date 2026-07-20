<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\Payment;
use App\Models\Standing;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Expense;
use App\Models\AuditLog;
use App\Services\TeamGeneratorService;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $nextMatch = FootballMatch::upcoming()->first();
        $myAvailability = $nextMatch
            ? Availability::where('match_id', $nextMatch->id)->where('user_id', $user->id)->value('status')
            : null;

        $recentPayments = Payment::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->latest()->take(5)->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'amount'     => $p->amount,
                'type'       => $p->type,
                'mpesa_code' => $p->mpesa_code,
                'date'       => $p->created_at,
            ]);

        $upcomingMatches = FootballMatch::upcoming()->take(4)->get()
            ->map(fn($m) => $this->formatMatch($m));

        return response()->json([
            'user'            => $this->formatUser($user),
            'next_match'      => $nextMatch ? $this->formatMatch($nextMatch) : null,
            'my_availability' => $myAvailability,
            'recent_payments' => $recentPayments,
            'upcoming_matches'=> $upcomingMatches,
        ]);
    }

    // ─── Matches ──────────────────────────────────────────────────────────────

    public function matches(Request $request)
    {
        $upcoming = FootballMatch::upcoming()->with('availabilities')->get()
            ->map(fn($m) => $this->formatMatch($m));

        $past = FootballMatch::past()->take(10)->get()
            ->map(fn($m) => $this->formatMatch($m));

        return response()->json([
            'upcoming' => $upcoming,
            'past'     => $past,
        ]);
    }

    public function matchDetails(Request $request, int $id)
    {
        $match = FootballMatch::with(['availabilities.user', 'matchTeams', 'creator'])->findOrFail($id);

        $user = $request->user();
        $myAvailability = Availability::where('match_id', $match->id)->where('user_id', $user->id)->value('status');

        return response()->json([
            'match'           => $this->formatMatch($match),
            'my_availability' => $myAvailability,
            'available_count' => $match->availabilities->where('status', 'available')->count(),
            'unavailable_count'=> $match->availabilities->where('status', 'unavailable')->count(),
            'maybe_count'     => $match->availabilities->where('status', 'maybe')->count(),
        ]);
    }

    public function updateAvailability(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:available,unavailable,maybe',
            'reason' => 'nullable|string|max:255',
        ]);

        $match = FootballMatch::findOrFail($id);
        $user  = $request->user();

        if ($match->isLocked() && !$user->hasRole(['admin', 'coach'])) {
            return response()->json(['message' => 'Availability is locked after the deadline.'], 403);
        }

        Availability::updateOrCreate(
            ['match_id' => $match->id, 'user_id' => $user->id],
            ['status' => $data['status'], 'reason' => $data['reason'] ?? null, 'changed_by' => $user->id]
        );

        return response()->json(['message' => 'Availability updated.', 'status' => $data['status']]);
    }

    public function lockAvailability(Request $request, int $id)
    {
        $match = FootballMatch::findOrFail($id);
        $match->update(['status' => 'locked']);
        Availability::where('match_id', $match->id)->update(['is_locked' => true]);

        return response()->json(['message' => 'Match availability locked.']);
    }

    public function generateTeams(Request $request, int $id)
    {
        $request->validate(['num_teams' => 'required|integer|min:2|max:3']);

        $match   = FootballMatch::findOrFail($id);
        $service = new TeamGeneratorService();
        $teams   = $service->generate($match, $request->num_teams);

        if (isset($teams['error'])) {
            return response()->json(['message' => $teams['error']], 422);
        }

        $service->save($match, $teams);

        return response()->json(['message' => 'Teams generated.', 'teams' => $teams]);
    }

    public function swapPlayers(Request $request, int $id)
    {
        $request->validate([
            'player1_id' => 'required|exists:users,id',
            'player2_id' => 'required|exists:users,id',
        ]);

        // Swap team assignments in match_teams table
        $match = FootballMatch::findOrFail($id);
        $t1 = $match->matchTeams()->where('user_id', $request->player1_id)->first();
        $t2 = $match->matchTeams()->where('user_id', $request->player2_id)->first();

        if ($t1 && $t2) {
            [$t1->team_name, $t2->team_name] = [$t2->team_name, $t1->team_name];
            $t1->save();
            $t2->save();
        }

        return response()->json(['message' => 'Players swapped.']);
    }

    public function recordResult(Request $request, int $id)
    {
        $data = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match = FootballMatch::findOrFail($id);
        $match->update(array_merge($data, ['status' => 'completed']));

        return response()->json(['message' => 'Result recorded.']);
    }

    // ─── Payments ─────────────────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $user = $request->user();

        $payments = Payment::where('user_id', $user->id)
            ->with('match')->latest()->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'amount'     => $p->amount,
                'type'       => $p->type,
                'status'     => $p->status,
                'mpesa_code' => $p->mpesa_code,
                'phone'      => $p->phone,
                'date'       => $p->created_at,
                'match'      => $p->match ? ['id' => $p->match->id, 'opponent' => $p->match->opponent] : null,
            ]);

        return response()->json(['payments' => $payments]);
    }

    public function initiateMpesaPay(Request $request)
    {
        $data = $request->validate([
            'phone'    => 'required|string',
            'amount'   => 'required|numeric|min:1',
            'match_id' => 'nullable|exists:matches,id',
        ]);

        $service = new MpesaService();
        $result  = $service->stkPush($data['phone'], $data['amount'], $data['match_id'] ?? null);

        return response()->json($result);
    }

    // ─── Standings ────────────────────────────────────────────────────────────

    public function standings(Request $request)
    {
        $season = $request->query('season', '2025/2026');

        $standings = Standing::with('team')
            ->where('season', $season)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->get()
            ->map(fn($s) => [
                'team'   => $s->team?->name,
                'played' => $s->played,
                'wins'   => $s->wins,
                'draws'  => $s->draws,
                'losses' => $s->losses,
                'gd'     => $s->goal_difference,
                'points' => $s->points,
            ]);

        return response()->json(['standings' => $standings]);
    }

    public function internalStandings(Request $request)
    {
        // Internal standings based on member match performance
        $members = User::where('role', 'member')->where('is_active', true)
            ->with(['availabilities' => fn($q) => $q->whereHas('match', fn($q2) => $q2->where('status', 'completed'))->where('status', 'available')])
            ->get()
            ->map(fn($u) => [
                'name'         => $u->name,
                'position'     => $u->position,
                'matches'      => $u->availabilities->count(),
                'jersey_number'=> $u->jersey_number,
            ])
            ->sortByDesc('matches')->values();

        return response()->json(['standings' => $members]);
    }

    // ─── Members ──────────────────────────────────────────────────────────────

    public function members(Request $request)
    {
        $members = User::where('role', 'member')->where('is_active', true)
            ->get()
            ->map(fn($u) => $this->formatUser($u));

        return response()->json(['members' => $members]);
    }

    // ─── Profile ──────────────────────────────────────────────────────────────

    public function profile(Request $request)
    {
        return response()->json(['user' => $this->formatUser($request->user())]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'phone'    => 'sometimes|string|max:20',
            'position' => 'sometimes|nullable|string',
        ]);

        $request->user()->update($data);

        return response()->json(['message' => 'Profile updated.', 'user' => $this->formatUser($request->user()->fresh())]);
    }

    // ─── Notifications ────────────────────────────────────────────────────────

    public function notifications(Request $request)
    {
        $announcements = Announcement::latest()->take(20)->get()
            ->map(fn($a) => [
                'id'      => $a->id,
                'title'   => $a->title,
                'body'    => $a->body,
                'date'    => $a->created_at,
                'is_read' => false, // extend with a pivot if needed
            ]);

        return response()->json(['notifications' => $announcements]);
    }

    public function markNotificationRead(Request $request, int $id)
    {
        // Mark as read — extend with AnnouncementRead pivot model if needed
        return response()->json(['message' => 'Marked as read.']);
    }

    // ─── Expenses ─────────────────────────────────────────────────────────────

    public function expenses(Request $request)
    {
        $expenses = Expense::where('is_approved', true)->latest()->get()
            ->map(fn($e) => [
                'id'       => $e->id,
                'title'    => $e->title,
                'amount'   => $e->amount,
                'category' => $e->category,
                'date'     => $e->expense_date,
            ]);

        return response()->json(['expenses' => $expenses]);
    }

    public function recordExpense(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:1',
            'category' => 'required|string',
            'notes'    => 'nullable|string',
        ]);

        $data['expense_date'] = now()->toDateString();
        $data['recorded_by']  = $request->user()->id;

        $expense = Expense::create($data);

        return response()->json(['message' => 'Expense recorded.', 'expense' => $expense], 201);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function formatUser(User $u): array
    {
        return [
            'id'            => $u->id,
            'name'          => $u->name,
            'email'         => $u->email,
            'role'          => $u->role,
            'position'      => $u->position,
            'phone'         => $u->phone,
            'jersey_number' => $u->jersey_number,
            'avatar_url'    => $u->avatar_url,
            'is_active'     => $u->is_active,
            'billing_type'  => $u->billing_type,
        ];
    }

    private function formatMatch(FootballMatch $m): array
    {
        return [
            'id'         => $m->id,
            'opponent'   => $m->opponent,
            'type'       => $m->type,
            'match_date' => $m->match_date,
            'match_time' => $m->match_time,
            'venue'      => $m->venue,
            'match_fee'  => $m->match_fee,
            'deadline'   => $m->deadline,
            'status'     => $m->status,
            'home_score' => $m->home_score,
            'away_score' => $m->away_score,
            'notes'      => $m->notes,
        ];
    }
}
