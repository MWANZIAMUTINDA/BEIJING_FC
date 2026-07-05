<?php

namespace App\Http\Controllers;

use App\Models\Standing;
use App\Models\LeagueResult;
use App\Models\LeagueTeam;
use App\Models\FootballMatch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
    public function standings()
    {
        $season   = request('season', '2025/2026');
        $standings = Standing::with('team')
            ->where('season', $season)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        $results = LeagueResult::with(['match', 'homeTeam', 'awayTeam', 'recorder'])
            ->latest()->take(10)->get();

        $seasons = Standing::distinct()->pluck('season');
        $teams   = LeagueTeam::where('is_active', true)->get();

        return view('league.standings', compact('standings', 'results', 'season', 'seasons', 'teams'));
    }

    public function history()
    {
        $results = LeagueResult::with(['match', 'homeTeam', 'awayTeam'])
            ->latest()->paginate(20);

        return view('league.history', compact('results'));
    }

    public function recordResult(Request $request)
    {
        $data = $request->validate([
            'match_id'     => 'required|exists:matches,id',
            'home_team_id' => 'required|exists:league_teams,id',
            'away_team_id' => 'required|exists:league_teams,id|different:home_team_id',
            'home_score'   => 'required|integer|min:0',
            'away_score'   => 'required|integer|min:0',
            'notes'        => 'nullable|string',
        ]);

        $data['recorded_by'] = auth()->id();
        $data['result']      = $data['home_score'] > $data['away_score']
            ? 'home_win'
            : ($data['home_score'] < $data['away_score'] ? 'away_win' : 'draw');

        // Check for duplicate
        if (LeagueResult::where('match_id', $data['match_id'])->exists()) {
            return back()->with('error', 'A result has already been recorded for this match.');
        }

        $result = LeagueResult::create($data);

        // Update the match status
        FootballMatch::where('id', $data['match_id'])->update([
            'home_score' => $data['home_score'],
            'away_score' => $data['away_score'],
            'status'     => 'completed',
        ]);

        // Update standings
        $this->updateStandings($result);

        AuditLog::record('league_result_recorded', $result, [], $data);

        return redirect()->route('league.standings')
            ->with('success', 'Result recorded and standings updated!');
    }

    private function updateStandings(LeagueResult $result): void
    {
        $season = '2025/2026';

        $homeStanding = Standing::firstOrCreate(
            ['league_team_id' => $result->home_team_id, 'season' => $season],
            ['played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0]
        );
        $awayStanding = Standing::firstOrCreate(
            ['league_team_id' => $result->away_team_id, 'season' => $season],
            ['played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0]
        );

        $homeStanding->increment('played');
        $homeStanding->increment('goals_for', $result->home_score);
        $homeStanding->increment('goals_against', $result->away_score);

        $awayStanding->increment('played');
        $awayStanding->increment('goals_for', $result->away_score);
        $awayStanding->increment('goals_against', $result->home_score);

        if ($result->result === 'home_win') {
            $homeStanding->increment('wins');
            $homeStanding->increment('points', 3);
            $awayStanding->increment('losses');
        } elseif ($result->result === 'away_win') {
            $awayStanding->increment('wins');
            $awayStanding->increment('points', 3);
            $homeStanding->increment('losses');
        } else {
            $homeStanding->increment('draws');
            $homeStanding->increment('points');
            $awayStanding->increment('draws');
            $awayStanding->increment('points');
        }

        $homeStanding->refresh();
        $awayStanding->refresh();

        $homeStanding->update(['goal_difference' => $homeStanding->goals_for - $homeStanding->goals_against]);
        $awayStanding->update(['goal_difference' => $awayStanding->goals_for - $awayStanding->goals_against]);
    }
}
