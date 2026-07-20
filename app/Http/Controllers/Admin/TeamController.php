<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeagueTeam;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = LeagueTeam::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teams = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:league_teams,name',
            'short_name' => 'required|string|max:5|unique:league_teams,short_name',
            'color'      => 'required|string|max:7',
            'kit_color'  => 'nullable|string|max:7',
            'is_active'  => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $data['is_active'] = false;
        }

        $team = LeagueTeam::create($data);

        AuditLog::record('team_created', $team, [], $data);

        return redirect()->route('admin.teams.index')
            ->with('success', "Team {$team->name} created successfully!");
    }

    public function show(LeagueTeam $team)
    {
        $team->load('players');
        return view('admin.teams.show', compact('team'));
    }

    public function edit(LeagueTeam $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, LeagueTeam $team)
    {
        $old = $team->only(['name', 'short_name', 'color', 'kit_color', 'is_active']);

        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:league_teams,name,' . $team->id,
            'short_name' => 'required|string|max:5|unique:league_teams,short_name,' . $team->id,
            'color'      => 'required|string|max:7',
            'kit_color'  => 'nullable|string|max:7',
            'is_active'  => 'boolean',
        ]);

        if (!$request->has('is_active')) {
            $data['is_active'] = false;
        }

        $team->update($data);

        AuditLog::record('team_updated', $team, $old, $data);

        return redirect()->route('admin.teams.index')
            ->with('success', "Team {$team->name} updated successfully!");
    }

    public function destroy(LeagueTeam $team)
    {
        // Remove players association
        User::where('league_team_id', $team->id)->update(['league_team_id' => null]);

        AuditLog::record('team_deleted', $team, [], ['name' => $team->name]);

        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', "Team {$team->name} deleted successfully.");
    }
}
