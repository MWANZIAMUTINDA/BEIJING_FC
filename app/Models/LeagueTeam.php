<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueTeam extends Model
{
    protected $table = 'league_teams';

    protected $fillable = ['name', 'short_name', 'color', 'kit_color', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function standings() { return $this->hasMany(Standing::class); }
    public function homeResults() { return $this->hasMany(LeagueResult::class, 'home_team_id'); }
    public function awayResults() { return $this->hasMany(LeagueResult::class, 'away_team_id'); }
    public function players() { return $this->hasMany(User::class, 'league_team_id'); }

    public function currentStanding(): ?Standing
    {
        return $this->standings()->where('season', config('app.current_season', '2025/2026'))->first();
    }
}
