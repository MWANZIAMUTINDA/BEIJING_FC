<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeagueTeam;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TeamApiController extends Controller
{
    /**
     * List all league teams.
     */
    public function index(Request $request)
    {
        $query = LeagueTeam::withCount('players');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)->orWhere('short_name','like',$s));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teams = $query->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $teams,
        ]);
    }

    /**
     * Get a team's details including its player roster.
     */
    public function show(LeagueTeam $team)
    {
        $team->load(['players' => fn($q) => $q->where('is_active', true)->orderBy('name')]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'           => $team->id,
                'name'         => $team->name,
                'short_name'   => $team->short_name,
                'color'        => $team->color,
                'kit_color'    => $team->kit_color,
                'is_active'    => $team->is_active,
                'player_count' => $team->players->count(),
                'players'      => $team->players->map(fn($p) => [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'position'      => $p->position,
                    'jersey_number' => $p->jersey_number,
                    'avatar_url'    => $p->avatar_url,
                ]),
            ],
        ]);
    }

    /**
     * Create a new team (admin only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:league_teams,name',
            'short_name' => 'required|string|max:5|unique:league_teams,short_name',
            'color'      => 'nullable|string|max:7',
            'kit_color'  => 'nullable|string|max:7',
            'is_active'  => 'boolean',
        ]);

        $team = LeagueTeam::create($data);
        AuditLog::record('team_api_created', $team, [], ['name' => $team->name]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Team created successfully.',
            'data'    => $team,
        ], 201);
    }

    /**
     * Update an existing team (admin only).
     */
    public function update(Request $request, LeagueTeam $team)
    {
        $data = $request->validate([
            'name'       => 'sometimes|string|max:100|unique:league_teams,name,' . $team->id,
            'short_name' => 'sometimes|string|max:5|unique:league_teams,short_name,' . $team->id,
            'color'      => 'sometimes|nullable|string|max:7',
            'kit_color'  => 'sometimes|nullable|string|max:7',
            'is_active'  => 'sometimes|boolean',
        ]);

        $old = $team->only(array_keys($data));
        $team->update($data);
        AuditLog::record('team_api_updated', $team, $old, $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Team updated successfully.',
            'data'    => $team,
        ]);
    }

    /**
     * Delete a team (admin only).
     */
    public function destroy(LeagueTeam $team)
    {
        if ($team->players()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete team with assigned players. Reassign or remove players first.',
            ], 422);
        }

        AuditLog::record('team_api_deleted', auth('sanctum')->user(), [], ['name' => $team->name]);
        $team->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Team deleted successfully.',
        ]);
    }
}
