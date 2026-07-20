<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalMatchGoal extends Model
{
    protected $table = 'internal_match_goals';

    protected $fillable = [
        'league_result_id', 'user_id', 'type', 'minute',
    ];

    public function leagueResult() { return $this->belongsTo(LeagueResult::class, 'league_result_id'); }
    public function player()       { return $this->belongsTo(User::class, 'user_id'); }
}
