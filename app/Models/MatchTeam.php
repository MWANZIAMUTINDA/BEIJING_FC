<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchTeam extends Model
{
    protected $table = 'match_teams';

    protected $fillable = [
        'match_id', 'team_label', 'players_list', 'position_balance_score',
    ];

    protected function casts(): array
    {
        return ['players_list' => 'array'];
    }

    public function match() { return $this->belongsTo(FootballMatch::class, 'match_id'); }

    public function getPlayers()
    {
        return User::whereIn('id', $this->players_list ?? [])->get();
    }
}
