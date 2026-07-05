<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'title', 'type', 'match_date', 'match_time', 'venue',
        'deadline', 'status', 'match_fee', 'home_team', 'away_team',
        'home_score', 'away_score', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'date',
            'deadline'   => 'datetime',
            'match_fee'  => 'decimal:2',
        ];
    }

    // Scopes
    public function scopeUpcoming($q)   { return $q->where('match_date', '>=', now()->toDateString())->orderBy('match_date'); }
    public function scopePast($q)       { return $q->where('match_date', '<', now()->toDateString())->orderByDesc('match_date'); }
    public function scopeLeague($q)     { return $q->where('type', 'league'); }
    public function scopeFriendly($q)   { return $q->where('type', 'friendly'); }

    // Helpers
    public function isPastDeadline(): bool  { return now()->greaterThan($this->deadline); }
    public function isLocked(): bool        { return in_array($this->status, ['locked', 'completed']) || $this->isPastDeadline(); }
    public function isCompleted(): bool     { return $this->status === 'completed'; }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'upcoming'  => ['label' => 'Upcoming',  'class' => 'badge-blue'],
            'open'      => ['label' => 'Open',      'class' => 'badge-green'],
            'locked'    => ['label' => 'Locked',    'class' => 'badge-orange'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-gray'],
            default     => ['label' => 'Unknown',   'class' => 'badge-gray'],
        };
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->match_date->format('D, d M Y');
    }

    // Relationships
    public function creator()        { return $this->belongsTo(User::class, 'created_by'); }
    public function availabilities() { return $this->hasMany(Availability::class, 'match_id'); }
    public function matchTeams()     { return $this->hasMany(MatchTeam::class, 'match_id'); }
    public function payments()       { return $this->hasMany(Payment::class, 'match_id'); }
    public function expenses()       { return $this->hasMany(Expense::class, 'match_id'); }
    public function leagueResult()   { return $this->hasOne(LeagueResult::class, 'match_id'); }

    public function availablePlayers()
    {
        return $this->availabilities()
            ->where('status', 'available')
            ->with('user');
    }
}
