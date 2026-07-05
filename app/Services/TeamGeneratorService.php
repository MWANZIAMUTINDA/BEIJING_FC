<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\MatchTeam;
use App\Models\User;
use Illuminate\Support\Collection;

class TeamGeneratorService
{
    private const POSITIONS_ORDER = ['GK', 'DF', 'MF', 'FW'];
    private const TEAM_LABELS = ['Team A', 'Team B', 'Team C'];

    public function generate(FootballMatch $match, int $numTeams = 2): array
    {
        // Get all available players
        $players = $this->getAvailablePlayers($match);

        if ($players->isEmpty()) {
            return ['error' => 'No available players found.'];
        }

        // Ensure at least 2 teams
        $numTeams = min($numTeams, 3);

        // Group players by position
        $byPosition = $players->groupBy('position');

        // Initialize team buckets
        $teams = [];
        for ($i = 0; $i < $numTeams; $i++) {
            $teams[$i] = ['label' => self::TEAM_LABELS[$i], 'players' => []];
        }

        // Distribute GKs first — one per team
        $gks = $byPosition->get('GK', collect());
        foreach ($gks->take($numTeams) as $i => $gk) {
            $teams[$i]['players'][] = $gk;
        }
        // Extra GKs go to outfield distribution
        $extraGks = $gks->slice($numTeams);

        // Distribute outfield players round-robin by position priority
        $outfield = collect();
        foreach (['DF', 'MF', 'FW'] as $pos) {
            $outfield = $outfield->merge($byPosition->get($pos, collect()));
        }
        $outfield = $outfield->merge($extraGks)->shuffle();

        $i = 0;
        foreach ($outfield as $player) {
            $teams[$i % $numTeams]['players'][] = $player;
            $i++;
        }

        // Calculate balance scores and build result
        $result = [];
        foreach ($teams as $team) {
            $playerIds   = collect($team['players'])->pluck('id')->toArray();
            $balanceScore = $this->calculateBalanceScore(collect($team['players']));
            $result[] = [
                'label'               => $team['label'],
                'players'             => collect($team['players']),
                'player_ids'          => $playerIds,
                'position_balance'    => $balanceScore,
                'count'               => count($playerIds),
            ];
        }

        return $result;
    }

    public function save(FootballMatch $match, array $generatedTeams): void
    {
        // Delete existing generated teams for this match
        MatchTeam::where('match_id', $match->id)->delete();

        foreach ($generatedTeams as $team) {
            MatchTeam::create([
                'match_id'               => $match->id,
                'team_label'             => $team['label'],
                'players_list'           => $team['player_ids'],
                'position_balance_score' => $team['position_balance'],
            ]);
        }
    }

    private function getAvailablePlayers(FootballMatch $match): Collection
    {
        return User::whereHas('availabilities', function ($q) use ($match) {
            $q->where('match_id', $match->id)->where('status', 'available');
        })->where('is_active', true)->get();
    }

    private function calculateBalanceScore(Collection $players): int
    {
        $positionCounts = $players->countBy('position');
        $hasGK  = $positionCounts->get('GK', 0) >= 1 ? 25 : 0;
        $hasDef = $positionCounts->get('DF', 0) >= 2 ? 25 : 0;
        $hasMid = $positionCounts->get('MF', 0) >= 2 ? 25 : 0;
        $hasFwd = $positionCounts->get('FW', 0) >= 1 ? 25 : 0;
        return $hasGK + $hasDef + $hasMid + $hasFwd;
    }
}
