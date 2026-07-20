<?php

namespace App\Http\Controllers;

use App\Models\Standing;
use App\Models\LeagueResult;
use App\Models\LeagueTeam;
use App\Models\InternalMatchGoal;
use App\Models\FootballMatch;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class InternalLeagueController extends Controller
{
    // The three internal team names
    private const INTERNAL_TEAMS = [
        ['name' => 'Team Red',   'short_name' => 'RED',  'color' => '#ef4444'],
        ['name' => 'Team Blue',  'short_name' => 'BLUE', 'color' => '#3b82f6'],
        ['name' => 'Team White', 'short_name' => 'WHT',  'color' => '#94a3b8'],
    ];

    private const SEASON = '2025/2026';

    /**
     * Ensure Red, Blue, White exist in league_teams.
     */
    private function ensureInternalTeams(): void
    {
        foreach (self::INTERNAL_TEAMS as $team) {
            LeagueTeam::firstOrCreate(
                ['short_name' => $team['short_name']],
                [
                    'name'     => $team['name'],
                    'color'    => $team['color'],
                    'is_active'=> true,
                ]
            );
        }
    }

    public function index()
    {
        $this->ensureInternalTeams();

        $internalShortNames = array_column(self::INTERNAL_TEAMS, 'short_name');

        // Standings for internal teams only
        $standings = Standing::with('team')
            ->whereHas('team', fn($q) => $q->whereIn('short_name', $internalShortNames))
            ->where('season', self::SEASON)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        // Recent results for internal teams only
        $results = LeagueResult::with(['homeTeam', 'awayTeam', 'recorder', 'goalEvents.player'])
            ->whereHas('homeTeam', fn($q) => $q->whereIn('short_name', $internalShortNames))
            ->latest()
            ->take(15)
            ->get();

        // ── Stats ─────────────────────────────────────────────────────────────

        // Get all league_result_ids that are internal
        $internalResultIds = LeagueResult::whereHas('homeTeam', fn($q) => $q->whereIn('short_name', $internalShortNames))
            ->pluck('id');

        // Top Scorers
        $topScorers = InternalMatchGoal::with('player')
            ->whereIn('league_result_id', $internalResultIds)
            ->where('type', 'goal')
            ->select('user_id', \DB::raw('count(*) as goals'))
            ->groupBy('user_id')
            ->orderByDesc('goals')
            ->take(5)
            ->get();

        // Most Assists
        $topAssists = InternalMatchGoal::with('player')
            ->whereIn('league_result_id', $internalResultIds)
            ->where('type', 'assist')
            ->select('user_id', \DB::raw('count(*) as assists'))
            ->groupBy('user_id')
            ->orderByDesc('assists')
            ->take(5)
            ->get();

        // Best Attendance (players who appear most often in team rosters of internal match days)
        // We count distinct league result appearances via match_teams player_ids
        $attendance = $this->getAttendanceStats($internalResultIds->toArray());

        $members = User::where('role', 'member')->where('is_active', true)->get();
        $internalTeams = LeagueTeam::whereIn('short_name', $internalShortNames)->get();

        return view('league.internal', compact(
            'standings', 'results', 'topScorers', 'topAssists', 'attendance',
            'members', 'internalTeams'
        ));
    }

    public function storeResult(Request $request)
    {
        $this->ensureInternalTeams();

        $data = $request->validate([
            'home_team_id'  => 'required|exists:league_teams,id',
            'away_team_id'  => 'required|exists:league_teams,id|different:home_team_id',
            'home_score'    => 'required|integer|min:0',
            'away_score'    => 'required|integer|min:0',
            'notes'         => 'nullable|string|max:500',
            // goal events: arrays of [user_id, type]
            'events'        => 'nullable|array',
            'events.*.user_id' => 'required|exists:users,id',
            'events.*.type'    => 'required|in:goal,assist',
        ]);

        // Find or create a dummy match row for internal games
        $match = FootballMatch::firstOrCreate(
            [
                'title'  => 'Internal Match',
                'type'   => 'friendly',
                'status' => 'upcoming',
            ],
            [
                'match_date' => now()->toDateString(),
                'match_time' => '15:00',
                'venue'      => 'Training Ground',
                'deadline'   => now()->subHour(),
                'match_fee'  => 0,
                'created_by' => auth()->id(),
            ]
        );

        $data['recorded_by'] = auth()->id();
        $data['match_id']    = $match->id;
        $data['result']      = $data['home_score'] > $data['away_score']
            ? 'home_win'
            : ($data['home_score'] < $data['away_score'] ? 'away_win' : 'draw');

        $result = LeagueResult::create($data);

        // Save goal/assist events
        if (!empty($data['events'])) {
            foreach ($data['events'] as $event) {
                InternalMatchGoal::create([
                    'league_result_id' => $result->id,
                    'user_id'          => $event['user_id'],
                    'type'             => $event['type'],
                ]);
            }
        }

        // Update standings
        $this->updateStandings($result);

        AuditLog::record('internal_league_result', $result, [], $data);

        return redirect()->route('league.internal')
            ->with('success', 'Internal match result recorded and standings updated!');
    }

    private function updateStandings(LeagueResult $result): void
    {
        $homeStanding = Standing::firstOrCreate(
            ['league_team_id' => $result->home_team_id, 'season' => self::SEASON],
            ['played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0]
        );
        $awayStanding = Standing::firstOrCreate(
            ['league_team_id' => $result->away_team_id, 'season' => self::SEASON],
            ['played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0, 'goal_difference' => 0, 'points' => 0]
        );

        $homeStanding->increment('played');
        $homeStanding->increment('goals_for',     $result->home_score);
        $homeStanding->increment('goals_against', $result->away_score);

        $awayStanding->increment('played');
        $awayStanding->increment('goals_for',     $result->away_score);
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
            $homeStanding->increment('draws'); $homeStanding->increment('points');
            $awayStanding->increment('draws'); $awayStanding->increment('points');
        }

        $homeStanding->refresh();
        $awayStanding->refresh();
        $homeStanding->update(['goal_difference' => $homeStanding->goals_for - $homeStanding->goals_against]);
        $awayStanding->update(['goal_difference' => $awayStanding->goals_for - $awayStanding->goals_against]);
    }

    private function getAttendanceStats(array $resultIds): \Illuminate\Support\Collection
    {
        // Count how many internal results each member has participated in
        // We approximate by counting InternalMatchGoal appearances + direct counting
        $all = InternalMatchGoal::whereIn('league_result_id', $resultIds)
            ->select('user_id', \DB::raw('count(distinct league_result_id) as matches'))
            ->groupBy('user_id')
            ->orderByDesc('matches')
            ->take(5)
            ->with('player')
            ->get();

        return $all;
    }
}
