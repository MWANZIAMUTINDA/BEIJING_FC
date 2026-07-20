<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\Availability;
use App\Models\MatchTeam;
use App\Models\User;
use Illuminate\Support\Collection;

class TeamGeneratorService
{
    // Team colour labels — matches Module 8 spec
    private const TEAM_LABELS  = ['Team Red', 'Team Blue', 'Team White'];
    private const TEAM_COLORS  = ['red', 'blue', 'white'];
    private const TEAM_EMOJIS  = ['🔴', '🔵', '⚪'];

    public function generate(FootballMatch $match, int $numTeams = 2): array
    {
        $players  = $this->getAvailablePlayers($match);

        if ($players->isEmpty()) {
            return ['error' => 'No available players found.'];
        }

        $numTeams = min(max($numTeams, 2), 3);

        // ── Step 2: Separate by position ─────────────────────────────────
        $byPosition = $players->groupBy('position');

        // ── Step 3: Balance players ───────────────────────────────────────
        $teams = [];
        for ($i = 0; $i < $numTeams; $i++) {
            $teams[$i] = [
                'label' => self::TEAM_LABELS[$i],
                'color' => self::TEAM_COLORS[$i],
                'emoji' => self::TEAM_EMOJIS[$i],
                'players' => [],
            ];
        }

        // Assign one GK per team first (most critical position)
        $gks      = $byPosition->get('GK', collect())->shuffle();
        $extraGks = collect();
        foreach ($gks as $i => $gk) {
            if ($i < $numTeams) {
                $teams[$i]['players'][] = $gk;
            } else {
                $extraGks->push($gk);
            }
        }

        // Distribute outfield players round-robin by position group
        // DF → MF → FW to ensure positional variety is spread evenly
        $outfield = collect();
        foreach (['DF', 'MF', 'FW'] as $pos) {
            $outfield = $outfield->merge($byPosition->get($pos, collect())->shuffle());
        }
        $outfield = $outfield->merge($extraGks)->shuffle();

        // Round-robin assignment — fills smallest team first
        foreach ($outfield as $player) {
            $smallest = collect($teams)->sortBy(fn($t) => count($t['players']))->keys()->first();
            $teams[$smallest]['players'][] = $player;
        }

        // ── Step 4: Build result with balance scores ──────────────────────
        $result = [];
        foreach ($teams as $team) {
            $col         = collect($team['players']);
            $breakdown   = $this->positionBreakdown($col);
            $score       = $this->calculateBalanceScore($col);
            $playerIds   = $col->pluck('id')->toArray();

            $result[] = [
                'label'            => $team['label'],
                'color'            => $team['color'],
                'emoji'            => $team['emoji'],
                'players'          => $col,
                'player_ids'       => $playerIds,
                'position_balance' => $score,
                'breakdown'        => $breakdown,
                'count'            => count($playerIds),
            ];
        }

        return $result;
    }

    /**
     * Calculate overall balance percentage across all generated teams.
     */
    public function overallBalance(array $generatedTeams): int
    {
        if (empty($generatedTeams)) return 0;
        $scores = array_map(fn($t) => $t['position_balance'], $generatedTeams);
        return (int) round(array_sum($scores) / count($scores));
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

    // ── Private helpers ───────────────────────────────────────────────────────

    private function getAvailablePlayers(FootballMatch $match): Collection
    {
        return User::whereHas('availabilities', function ($q) use ($match) {
            $q->where('match_id', $match->id)->where('status', 'available');
        })->where('is_active', true)->get();
    }

    private function positionBreakdown(Collection $players): array
    {
        $counts = $players->countBy('position');
        return [
            'GK' => $counts->get('GK', 0),
            'DF' => $counts->get('DF', 0),
            'MF' => $counts->get('MF', 0),
            'FW' => $counts->get('FW', 0),
        ];
    }

    private function calculateBalanceScore(Collection $players): int
    {
        $counts = $players->countBy('position');
        $gk  = $counts->get('GK', 0) >= 1 ? 30 : 0;
        $def = $counts->get('DF', 0) >= 2 ? 25 : ($counts->get('DF', 0) === 1 ? 12 : 0);
        $mid = $counts->get('MF', 0) >= 2 ? 25 : ($counts->get('MF', 0) === 1 ? 12 : 0);
        $fwd = $counts->get('FW', 0) >= 1 ? 20 : 0;
        return min(100, $gk + $def + $mid + $fwd);
    }
}
