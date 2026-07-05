<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    protected $fillable = [
        'league_team_id', 'season', 'played', 'wins', 'draws',
        'losses', 'goals_for', 'goals_against', 'goal_difference', 'points',
    ];

    protected function casts(): array
    {
        return [
            'played' => 'integer', 'wins' => 'integer', 'draws' => 'integer',
            'losses' => 'integer', 'goals_for' => 'integer',
            'goals_against' => 'integer', 'goal_difference' => 'integer',
            'points' => 'integer',
        ];
    }

    public function team() { return $this->belongsTo(LeagueTeam::class, 'league_team_id'); }

    public function recalculate(): void
    {
        $this->goal_difference = $this->goals_for - $this->goals_against;
        $this->save();
    }
}
