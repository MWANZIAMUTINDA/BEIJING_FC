<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'type', 'send_sms',
        'sent_at', 'created_by', 'match_id',
    ];

    protected function casts(): array
    {
        return [
            'send_sms' => 'boolean',
            'sent_at'  => 'datetime',
        ];
    }

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function match()   { return $this->belongsTo(FootballMatch::class, 'match_id'); }
    public function reads()   { return $this->hasMany(NotificationRead::class); }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'match_reminder'  => 'Match Reminder',
            'payment_alert'   => 'Payment Alert',
            'league_update'   => 'League Update',
            'urgent'          => 'Urgent',
            default           => 'General',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            'match_reminder' => 'badge-blue',
            'payment_alert'  => 'badge-yellow',
            'league_update'  => 'badge-emerald',
            'urgent'         => 'badge-red',
            default          => 'badge-gray',
        };
    }
}
