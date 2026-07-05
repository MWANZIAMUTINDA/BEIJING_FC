<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueResult extends Model
{
    protected $fillable = [
        'match_id', 'home_team_id', 'away_team_id',
        'home_score', 'away_score', 'result', 'notes', 'recorded_by',
    ];

    public function match()    { return $this->belongsTo(FootballMatch::class, 'match_id'); }
    public function homeTeam() { return $this->belongsTo(LeagueTeam::class, 'home_team_id'); }
    public function awayTeam() { return $this->belongsTo(LeagueTeam::class, 'away_team_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }

    public function getScoreline(): string
    {
        return "{$this->home_score} – {$this->away_score}";
    }
}
