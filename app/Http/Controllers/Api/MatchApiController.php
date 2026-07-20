<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\AuditLog;
use App\Models\MatchTeam;
use App\Services\TeamGeneratorService;
use Illuminate\Http\Request;

class MatchApiController extends Controller
{
    /**
     * Fetch upcoming and past matches list for the mobile app feed.
     */
    public function index()
    {
        $upcoming = FootballMatch::upcoming()
            ->with(['creator'])
            ->orderBy('match_date', 'asc')
            ->get();

        $past = FootballMatch::past()
            ->with(['leagueResult'])
            ->orderBy('match_date', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'upcoming' => $upcoming,
                'past' => $past,
            ]
        ]);
    }

    /**
     * Get specific match details along with rosters and availability status.
     */
    public function show(FootballMatch $match)
    {
        $match->load(['creator', 'availabilities.user', 'matchTeams', 'leagueResult']);

        $userAvailability = null;
        if (auth('sanctum')->check()) {
            $userAvailability = $match->availabilities()
                ->where('user_id', auth('sanctum')->id())
                ->first();
        }

        $availableCount   = $match->availabilities->where('status', 'available')->count();
        $maybeCount       = $match->availabilities->where('status', 'maybe')->count();
        $unavailableCount = $match->availabilities->where('status', 'unavailable')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'match' => $match,
                'my_availability' => $userAvailability,
                'availabilities_summary' => [
                    'available' => $availableCount,
                    'maybe' => $maybeCount,
                    'unavailable' => $unavailableCount,
                ]
            ]
        ]);
    }

    /**
     * Submit or update user availability response for a match.
     */
    public function updateAvailability(Request $request, FootballMatch $match)
    {
        $data = $request->validate([
            'status'  => 'required|in:available,unavailable,maybe',
            'user_id' => 'nullable|exists:users,id',
            'reason'  => 'nullable|string|max:255',
        ]);

        $targetUserId = auth()->id();
        $isAdminOrCoach = auth()->user()->hasRole(['admin', 'coach']);

        if ($request->filled('user_id')) {
            if (!$isAdminOrCoach && $request->user_id != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. You can only update your own availability.'
                ], 403);
            }
            $targetUserId = $request->user_id;
        }

        $isLocked = $match->isLocked();
        if ($isLocked && !$isAdminOrCoach) {
            return response()->json([
                'status' => 'error',
                'message' => 'Availability is locked after the deadline.'
            ], 403);
        }

        $targetUser = \App\Models\User::findOrFail($targetUserId);
        $old = Availability::where('match_id', $match->id)->where('user_id', $targetUserId)->first();
        $oldStatus = $old ? $old->status : 'none';

        $availability = Availability::updateOrCreate(
            ['match_id' => $match->id, 'user_id' => $targetUserId],
            [
                'status'         => $data['status'],
                'reason'         => $data['reason'] ?? null,
                'changed_by'     => auth()->id(),
                'admin_override' => $isLocked && $isAdminOrCoach,
                'is_locked'      => $isLocked,
            ]
        );

        // Audit Trail
        if ($isLocked && $isAdminOrCoach) {
            AuditLog::record('admin_availability_override', $availability, 
                ['old_status' => $oldStatus], 
                [
                    'match_opponent' => $match->opponent,
                    'player_name'    => $targetUser->name,
                    'status'         => $data['status'],
                    'reason'         => $data['reason'] ?? 'Override'
                ]
            );
        } else {
            AuditLog::record('availability_updated', $availability, 
                ['old_status' => $oldStatus], 
                [
                    'match_opponent' => $match->opponent,
                    'status'         => $data['status']
                ]
            );
        }

        if ($match->status === 'completed' && $targetUser->billing_type === 'match') {
            \App\Models\MemberBalance::recalculate($targetUserId);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Availability updated successfully.',
            'data' => $availability
        ]);
    }

    /**
     * Lock availability submissions.
     */
    public function lockAvailability(FootballMatch $match)
    {
        if (!auth()->user()->hasRole(['admin', 'coach'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        $match->update(['status' => 'locked']);
        Availability::where('match_id', $match->id)->update(['is_locked' => true]);
        AuditLog::record('match_locked', $match);

        return response()->json([
            'status' => 'success',
            'message' => 'Match availability submissions locked successfully.'
        ]);
    }

    /**
     * Generate balanced teams (admin or coach only).
     */
    public function generateTeams(Request $request, FootballMatch $match)
    {
        if (!auth()->user()->hasRole(['admin', 'coach'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        $request->validate(['num_teams' => 'required|integer|min:2|max:3']);

        $service = new TeamGeneratorService();
        $teams   = $service->generate($match, $request->num_teams);

        if (isset($teams['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => $teams['error']
            ], 400);
        }

        $service->save($match, $teams);
        AuditLog::record('teams_generated', $match);

        return response()->json([
            'status' => 'success',
            'message' => 'Balanced teams generated successfully.',
            'data' => MatchTeam::where('match_id', $match->id)->get()
        ]);
    }

    /**
     * Swap players between squads manually.
     */
    public function swapPlayers(Request $request, FootballMatch $match)
    {
        if (!auth()->user()->hasRole(['admin', 'coach'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'player_id' => 'required|exists:users,id',
            'from_team' => 'required|string',
            'to_team'   => 'required|string',
        ]);

        $teams = MatchTeam::where('match_id', $match->id)->get()->keyBy('team_label');

        $from = $teams->get($request->from_team);
        $to   = $teams->get($request->to_team);

        if (!$from || !$to) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid team selection.'
            ], 400);
        }

        $playerId = (int) $request->player_id;

        $fromList = array_values(array_filter($from->players_list ?? [], fn($id) => $id != $playerId));
        $toList = array_merge($to->players_list ?? [], [$playerId]);

        $from->update(['players_list' => $fromList]);
        $to->update(['players_list' => $toList]);

        AuditLog::record('team_swap', $match, [], [
            'player_id' => $playerId,
            'from'      => $request->from_team,
            'to'        => $request->to_team,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Player swapped successfully.'
        ]);
    }

    /**
     * Record match result and change status to completed.
     */
    public function recordResult(Request $request, FootballMatch $match)
    {
        if (!auth()->user()->hasRole(['admin', 'coach'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        $data = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match->update(array_merge($data, ['status' => 'completed']));
        AuditLog::record('result_recorded', $match, [], $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Match scoreline recorded successfully.',
            'data' => $match
        ]);
    }

    /**
     * Create a new match (admin or coach only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:100',
            'away_team'  => 'required|string|max:100',
            'type'       => 'required|in:league,friendly',
            'match_date' => 'required|date|after_or_equal:today',
            'match_time' => 'required',
            'venue'      => 'required|string|max:200',
            'deadline'   => 'required|date',
            'match_fee'  => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['status']     = 'upcoming';

        $match = FootballMatch::create($data);
        AuditLog::record('match_api_created', $match, [], $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Match scheduled successfully.',
            'data'    => $match,
        ], 201);
    }

    /**
     * Update an existing match (admin or coach only).
     */
    public function update(Request $request, FootballMatch $match)
    {
        $data = $request->validate([
            'title'      => 'sometimes|nullable|string|max:100',
            'away_team'  => 'sometimes|required|string|max:100',
            'type'       => 'sometimes|required|in:league,friendly',
            'match_date' => 'sometimes|required|date',
            'match_time' => 'sometimes|required',
            'venue'      => 'sometimes|required|string|max:200',
            'deadline'   => 'sometimes|required|date',
            'match_fee'  => 'sometimes|required|numeric|min:0',
            'status'     => 'sometimes|required|in:upcoming,open,locked,completed',
            'notes'      => 'sometimes|nullable|string',
        ]);

        $old = $match->only(array_keys($data));
        $match->update($data);
        AuditLog::record('match_api_updated', $match, $old, $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Match updated successfully.',
            'data'    => $match,
        ]);
    }

    /**
     * Delete an existing match (admin or coach only).
     */
    public function destroy(FootballMatch $match)
    {
        AuditLog::record('match_api_deleted', auth()->user(), [], ['opponent' => $match->opponent]);
        $match->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Match deleted successfully.',
        ]);
    }

    /**
     * Filter matches dynamically.
     */
    public function filter(Request $request)
    {
        $query = FootballMatch::query();

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('away_team','like',$s)->orWhere('title','like',$s)->orWhere('venue','like',$s));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->where('match_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('match_date', '<=', $request->end_date);
        }

        $matches = $query->orderBy('match_date', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $matches,
        ]);
    }
}

