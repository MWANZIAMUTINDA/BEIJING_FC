<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Standing;
use App\Models\LeagueResult;
use App\Models\LeagueTeam;
use Illuminate\Http\Request;

class LeagueApiController extends Controller
{
    /**
     * Get external league standings and results.
     */
    public function standings(Request $request)
    {
        $season = $request->input('season', '2025/2026');

        $standings = Standing::with('team')
            ->where('season', $season)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        $results = LeagueResult::with(['match', 'homeTeam', 'awayTeam'])
            ->whereHas('homeTeam', fn($q) => $q->whereNotIn('short_name', ['RED', 'BLUE', 'WHT']))
            ->latest()
            ->take(15)
            ->get();

        return response()->json([
            'status' => 'success',
            'season' => $season,
            'data' => [
                'standings' => $standings,
                'results' => $results
            ]
        ]);
    }

    /**
     * Get internal league standings and results.
     */
    public function internalStandings()
    {
        $season = '2025/2026';
        $internalShortNames = ['RED', 'BLUE', 'WHT'];

        $standings = Standing::with('team')
            ->whereHas('team', fn($q) => $q->whereIn('short_name', $internalShortNames))
            ->where('season', $season)
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for')
            ->get();

        $results = LeagueResult::with(['homeTeam', 'awayTeam', 'recorder', 'goalEvents.player'])
            ->whereHas('homeTeam', fn($q) => $q->whereIn('short_name', $internalShortNames))
            ->latest()
            ->take(15)
            ->get();

        return response()->json([
            'status' => 'success',
            'season' => $season,
            'data' => [
                'standings' => $standings,
                'results' => $results
            ]
        ]);
    }
}
