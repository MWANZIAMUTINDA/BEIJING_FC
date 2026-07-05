<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\AuditLog;
use App\Services\TeamGeneratorService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MatchController extends Controller
{
    public function index()
    {
        $upcoming = FootballMatch::upcoming()->with(['availabilities', 'creator'])->paginate(8);
        $past     = FootballMatch::past()->with(['leagueResult'])->take(10)->get();
        return view('matches.index', compact('upcoming', 'past'));
    }

    public function create()
    {
        return view('matches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:100',
            'type'       => 'required|in:league,friendly',
            'match_date' => 'required|date|after:today',
            'match_time' => 'required',
            'venue'      => 'required|string|max:150',
            'deadline'   => 'required|date|before:match_date',
            'match_fee'  => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['status']     = 'upcoming';

        $match = FootballMatch::create($data);

        AuditLog::record('match_created', $match, [], $data);

        return redirect()->route('matches.show', $match)->with('success', 'Match created successfully!');
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

    public function destroy(FootballMatch $match)
    {
        AuditLog::record('match_deleted', $match);
        $match->delete();
        return redirect()->route('matches.index')->with('success', 'Match deleted.');
    }
}
