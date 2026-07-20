<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\AuditLog;
use App\Models\MatchTeam;
use App\Models\Stadium;
use App\Services\TeamGeneratorService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $upcomingQuery = FootballMatch::upcoming()->with(['availabilities', 'creator']);
        $pastQuery     = FootballMatch::past()->with(['leagueResult']);

        // Search filter
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $upcomingQuery->where(fn($q) => $q->where('away_team','like',$s)->orWhere('title','like',$s)->orWhere('venue','like',$s));
            $pastQuery->where(fn($q)     => $q->where('away_team','like',$s)->orWhere('title','like',$s)->orWhere('venue','like',$s));
        }
        if ($request->filled('type') && in_array($request->type, ['league','friendly'])) {
            $upcomingQuery->where('type', $request->type);
            $pastQuery->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $upcomingQuery->where('status', $request->status);
        }

        $upcoming = $upcomingQuery->paginate(8)->withQueryString();
        $past     = $pastQuery->paginate(10)->withQueryString();
        return view('matches.index', compact('upcoming', 'past'));
    }

    public function create()
    {
        $stadiums = Stadium::active()->orderBy('name')->get();
        return view('matches.create', compact('stadiums'));
    }

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

        // Soft duplicate warning: same date + same hour:minute already exists
        // Normalize to HH:MM to handle both '16:00' and '16:00:00' formats
        $timePrefix = substr($data['match_time'], 0, 5); // e.g. '16:00'
        $duplicate = FootballMatch::where('match_date', 'like', $data['match_date'] . '%')
            ->whereRaw("SUBSTR(match_time, 1, 5) = ?", [$timePrefix])
            ->exists();

        $match = FootballMatch::create($data);
        AuditLog::record('match_created', $match, [], $data);

        $msg = 'Match scheduled successfully!';
        if ($duplicate) {
            $msg .= ' ⚠️ Warning: another match is already scheduled at the same date & time.';
        }

        return redirect()->route('matches.show', $match)->with('success', $msg);
    }

    public function edit(FootballMatch $match)
    {
        $stadiums = Stadium::active()->orderBy('name')->get();
        return view('matches.edit', compact('match', 'stadiums'));
    }

    public function update(Request $request, FootballMatch $match)
    {
        $old = $match->only(['title','away_team','type','match_date','match_time','venue','deadline','match_fee','status','notes']);

        $data = $request->validate([
            'title'      => 'nullable|string|max:100',
            'away_team'  => 'required|string|max:100',
            'type'       => 'required|in:league,friendly',
            'match_date' => 'required|date',
            'match_time' => 'required',
            'venue'      => 'required|string|max:200',
            'deadline'   => 'required|date',
            'match_fee'  => 'required|numeric|min:0',
            'status'     => 'required|in:upcoming,open,locked,completed',
            'notes'      => 'nullable|string',
        ]);

        $match->update($data);
        AuditLog::record('match_updated', $match, $old, $data);

        return redirect()->route('matches.show', $match)->with('success', 'Match updated successfully!');
    }

    public function show(FootballMatch $match)
    {
        $match->load(['creator', 'availabilities.user', 'matchTeams', 'leagueResult']);

        $members = \App\Models\User::where('role', 'member')
            ->where('is_active', true)
            ->get()
            ->map(function ($user) use ($match) {
                $user->availability = $user->availabilityFor($match->id);
                return $user;
            });

        $availableCount   = $match->availabilities->where('status', 'available')->count();
        $unavailableCount = $match->availabilities->where('status', 'unavailable')->count();
        $maybeCount       = $match->availabilities->where('status', 'maybe')->count();
        $noResponse       = $members->count() - $match->availabilities->count();

        return view('matches.show', compact(
            'match', 'members', 'availableCount', 'unavailableCount', 'maybeCount', 'noResponse'
        ));
    }

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
                return back()->with('error', 'Unauthorized. You can only update your own availability.');
            }
            $targetUserId = $request->user_id;
        }

        // If locked/deadline passed, only admin/coach can change
        $isLocked = $match->isLocked();
        if ($isLocked && !$isAdminOrCoach) {
            return back()->with('error', 'Availability is locked after the deadline.');
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

        // Recalculate member balance if this is a completed match and they are pay per match
        if ($match->status === 'completed' && $targetUser->billing_type === 'match') {
            \App\Models\MemberBalance::recalculate($targetUserId);
        }

        return back()->with('success', 'Availability response has been saved successfully!');
    }

    public function lockAvailability(FootballMatch $match)
    {
        $match->update(['status' => 'locked']);
        Availability::where('match_id', $match->id)->update(['is_locked' => true]);
        AuditLog::record('match_locked', $match);

        return back()->with('success', 'Match availability locked.');
    }

    public function generateTeams(Request $request, FootballMatch $match)
    {
        $request->validate(['num_teams' => 'required|integer|min:2|max:3']);

        $service = new TeamGeneratorService();
        $teams   = $service->generate($match, $request->num_teams);

        if (isset($teams['error'])) {
            return back()->with('error', $teams['error']);
        }

        $service->save($match, $teams);
        AuditLog::record('teams_generated', $match);

        return redirect()->route('matches.show', $match)
            ->with('success', 'Teams generated successfully!');
    }

    public function recordResult(Request $request, FootballMatch $match)
    {
        $data = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match->update(array_merge($data, ['status' => 'completed']));
        AuditLog::record('result_recorded', $match, [], $data);

        return back()->with('success', 'Result recorded!');
    }

    public function swapPlayers(Request $request, FootballMatch $match)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'from_team' => 'required|string',
            'to_team'   => 'required|string',
        ]);

        $teams = MatchTeam::where('match_id', $match->id)->get()->keyBy('team_label');

        $from = $teams->get($request->from_team);
        $to   = $teams->get($request->to_team);

        if (!$from || !$to) {
            return back()->with('error', 'Invalid team selection.');
        }

        $playerId = (int) $request->player_id;

        // Remove from source team
        $fromList = array_values(array_filter($from->players_list ?? [], fn($id) => $id != $playerId));
        // Add to destination team
        $toList = array_merge($to->players_list ?? [], [$playerId]);

        $from->update(['players_list' => $fromList]);
        $to->update(['players_list' => $toList]);

        AuditLog::record('team_swap', $match, [], [
            'player_id' => $playerId,
            'from'      => $request->from_team,
            'to'        => $request->to_team,
        ]);

        return back()->with('success', 'Player swapped successfully!');
    }

    public function destroy(FootballMatch $match)
    {
        AuditLog::record('match_deleted', $match);
        $match->delete();
        return redirect()->route('matches.index')->with('success', 'Match deleted.');
    }
}
